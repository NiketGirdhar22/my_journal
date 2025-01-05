<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$userDir = "assets/usrdata/{$username}";
$entriesFilePath = "{$userDir}/entries.json";
$infoFilePath = "{$userDir}/info.json";

if (!file_exists($infoFilePath)) {
    die("Encryption key file (info.json) does not exist.");
}

$infoData = json_decode(file_get_contents($infoFilePath), true);
if (!isset($infoData['encryptionKey'])) {
    die("Encryption key is missing in info.json.");
}

$encryptionKey = $infoData['encryptionKey'];
$encryptionMethod = 'AES-256-CBC';

// Function to encrypt data
function encrypt($data, $key, $method) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $encryptedData = openssl_encrypt($data, $method, $key, 0, $iv);
    return base64_encode($encryptedData . '::' . $iv);
}

function decrypt($data, $key, $method) {
    list($encryptedData, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encryptedData, $method, $key, 0, $iv);
}

$entries = [];

if (is_dir($userDir) && file_exists($entriesFilePath)) {
    $entriesData = file_get_contents($entriesFilePath);
    $entries = json_decode($entriesData, true) ?: [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entryTitle = trim($_POST['entryTitle']);
    $entryContent = trim($_POST['entryContent']);

    if (empty($entryTitle) || empty($entryContent)) {
        die("<script type='text/javascript'>
                alert('Title and content cannot be empty!');
                window.history.back();
              </script>");
    }

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'save') {
            $encryptedTitle = encrypt($entryTitle, $encryptionKey, $encryptionMethod);
            $encryptedContent = encrypt($entryContent, $encryptionKey, $encryptionMethod);

            $newEntry = [
                'title' => $encryptedTitle,
                'content' => $encryptedContent,
                'date' => date('Y-m-d H:i:s')
            ];

            $entries[] = $newEntry;
        } elseif ($_POST['action'] === 'update' && isset($_POST['entryIndex'])) {
            $entryIndex = (int) $_POST['entryIndex'];
            if (isset($entries[$entryIndex])) {
                $entries[$entryIndex]['title'] = encrypt($entryTitle, $encryptionKey, $encryptionMethod);
                $entries[$entryIndex]['content'] = encrypt($entryContent, $encryptionKey, $encryptionMethod);
                $entries[$entryIndex]['date'] = date('Y-m-d H:i:s');
            }
        } elseif ($_POST['action'] === 'delete' && isset($_POST['entryIndex'])) {
            $entryIndex = (int) $_POST['entryIndex'];
            if (isset($entries[$entryIndex])) {
                array_splice($entries, $entryIndex, 1);
            }
        }
    }

    if (file_put_contents($entriesFilePath, json_encode($entries, JSON_PRETTY_PRINT)) === false) {
        die("<script type='text/javascript'>
                alert('Failed to save entries.');
                window.history.back();
              </script>");
    }

    echo "<script type='text/javascript'>
            alert('Entry successfully saved!');
            window.location.href = 'welcomeback.php';  // Redirect to the welcome page
          </script>";
}
?>
