<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$currentUser = $_SESSION['username'];
$userFolder = "assets/usrdata/$currentUser";
$infoFile = "$userFolder/info.json";
$message = "";

if (!file_exists($infoFile)) {
    $message = "User info file not found!";
} else {
    $userData = json_decode(file_get_contents($infoFile), true);

    $lastUpdate = $userData['update_username'];
    $canUpdate = true;

    if ($lastUpdate) {
        $lastUpdateTimestamp = strtotime($lastUpdate);
        $currentTimestamp = time();
        $daysSinceLastUpdate = ($currentTimestamp - $lastUpdateTimestamp) / (60 * 60 * 24);

        if ($daysSinceLastUpdate < 10) {
            $canUpdate = false;
            $message = "You can only update your username once every 10 days. Last update was on $lastUpdate.";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canUpdate) {
        $newUsername = trim($_POST['newUsername']);

        if (empty($newUsername)) {
            $message = "New username cannot be empty.";
        } elseif ($newUsername === $currentUser) {
            $message = "New username cannot be the same as the current username.";
        } elseif (file_exists("assets/usrdata/$newUsername")) {
            $message = "Username is already taken.";
        } else {
            $newFolder = "assets/usrdata/$newUsername";

            if (rename($userFolder, $newFolder)) {
                $userData['username'] = $newUsername;
                $userData['update_username'] = date("Y-m-d H:i:s");
                file_put_contents("$newFolder/info.json", json_encode($userData, JSON_PRETTY_PRINT));

                $_SESSION['username'] = $newUsername;

                header("Location: welcomeback.php");
                exit();
            } else {
                $message = "Error updating username. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyJournal - Update Username</title>
    <link rel="stylesheet" href="assets/css/login_styles.css">
</head>
<body>
    <div class="overlay"></div>
    <div class="login-container">
        <div class="login-box">
            <h2>Update Username</h2>
            <?php if (!empty($message)) { echo "<p class='error'>$message</p>"; } ?>
            <br>
            <form method="POST">
                <div class="input-group">
                    <label for="newUsername">New Username</label>
                    <input type="text" id="newUsername" name="newUsername" required>
                </div>
                <button type="submit" class="login-button">Update Username</button>
                <div class="links">
                    <a href="welcomeback.php">Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
