<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$userDir = "assets/usrdata/{$username}";
$infoFile = "{$userDir}/info.json";

function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }

    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $filePath = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($filePath)) {
            deleteDirectory($filePath);
        } else {
            unlink($filePath);
        }
    }

    return rmdir($dir);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $enteredPassword = $_POST['password'];

    if (file_exists($infoFile)) {
        $infoData = json_decode(file_get_contents($infoFile), true);

        if (password_verify($enteredPassword, $infoData['password'])) {
            if (deleteDirectory($userDir)) {
                session_unset();
                session_destroy();

                echo "<script>
                    alert('Your account has been deleted successfully.');
                    window.location.href = 'index.html';
                </script>";
            } else {
                echo "<script>
                    alert('An error occurred while deleting your account. Please try again later.');
                    window.history.back();
                </script>";
            }
        } else {
            echo "<script>
                alert('Incorrect password. Please try again.');
                window.history.back();
            </script>";
        }
    } else {
        echo "<script>
            alert('Account data not found. Please contact support.');
            window.history.back();
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyJournal - Login</title>
    <link rel="stylesheet" href="assets/css/login_styles.css">
</head>
<body>
    <div class="overlay"></div>
    <div class="login-container">
        <div class="login-box">
            <h2>Delete Account</h2>
            <h2></h2>
            <p>Please confirm your password to delete your account. This action cannot be undone.</p>
            <h2></h2>
            <form method="POST">
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-button">Delete Account</button>
                <div class="links">
                <a></a>
                <a href="welcomeback.php">Cancel</a>
            </div>
            </form>
        </div>
    </div>
</body>
</html>