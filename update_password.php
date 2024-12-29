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

    $lastPasswordUpdate = $userData['update_password'] ?? null;
    $canUpdate = true;

    if ($lastPasswordUpdate) {
        $lastUpdateTimestamp = strtotime($lastPasswordUpdate);
        $currentTimestamp = time();
        $daysSinceLastUpdate = ($currentTimestamp - $lastUpdateTimestamp) / (60 * 60 * 24);

        if ($daysSinceLastUpdate < 10) {
            $canUpdate = false;
            $message = "You can only update your password once every 10 days. Last update was on $lastPasswordUpdate.";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canUpdate) {
        $currentPassword = trim($_POST['current_password']);
        $newPassword = trim($_POST['new_password']);
        $confirmPassword = trim($_POST['confirm_password']);

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $message = "All fields are required.";
        } elseif (!password_verify($currentPassword, $userData['password'])) {
            $message = "Current password is incorrect.";
        } elseif ($newPassword !== $confirmPassword) {
            $message = "New password and confirmation do not match.";
        } else {
            $userData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            $userData['update_password'] = date("Y-m-d H:i:s");
            file_put_contents($infoFile, json_encode($userData, JSON_PRETTY_PRINT));

            $message = "Password updated successfully!";
            header("Location: welcomeback.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyJournal - Update Password</title>
    <link rel="stylesheet" href="assets/css/login_styles.css">
</head>
<body>
    <div class="overlay"></div>
    <div class="login-container">
        <div class="login-box">
            <h2>Update Password</h2>
            <?php if (!empty($message)): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <br>
            <form method="post">
                <div class="input-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="input-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="login-button">Update Password</button>
                <div class="links">
                    <a href="welcomeback.php">Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
