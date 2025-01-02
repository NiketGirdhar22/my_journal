<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$userDir = "assets/usrdata/{$username}";
$entriesFilePath = "{$userDir}/entries.json";

$entries = [];

if (is_dir($userDir) && file_exists($entriesFilePath)) {
    $entriesData = file_get_contents($entriesFilePath);
    $entries = json_decode($entriesData, true) ?: [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entryTitle = trim($_POST['entryTitle']);
    $entryContent = trim($_POST['entryContent']);

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'save') {
            $newEntry = [
                'title' => $entryTitle,
                'content' => $entryContent,
                'date' => date('Y-m-d H:i:s')
            ];
            $entries[] = $newEntry;
        } elseif ($_POST['action'] === 'update' && isset($_POST['entryIndex'])) {
            $entryIndex = (int) $_POST['entryIndex'];
            if (isset($entries[$entryIndex])) {
                $entries[$entryIndex]['title'] = $entryTitle;
                $entries[$entryIndex]['content'] = $entryContent;
                $entries[$entryIndex]['date'] = date('Y-m-d H:i:s');
            }
        } elseif ($_POST['action'] === 'delete' && isset($_POST['entryIndex'])) {

            $entryIndex = (int) $_POST['entryIndex'];
            if (isset($entries[$entryIndex])) {
                array_splice($entries, $entryIndex, 1);
            }
        }
    }

    file_put_contents($entriesFilePath, json_encode($entries, JSON_PRETTY_PRINT));

    header("Location: welcomeback.php");
    exit();
}
