<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$userDirectory = "assets/usrdata";

$username = isset($_GET['username']) ? htmlspecialchars(trim($_GET['username'])) : (isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : '');
$token = isset($_GET['token']) ? htmlspecialchars(trim($_GET['token'])) : (isset($_POST['token']) ? htmlspecialchars(trim($_POST['token'])) : '');

if (empty($username) || empty($token)) {
    echo "<script>alert('Username or token missing.');</script>";
    echo "<script>window.location.href='forgot.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = htmlspecialchars(trim($_POST['new_password']));
    $confirmPassword = htmlspecialchars(trim($_POST['confirm_password']));

    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
        echo "<script>window.history.back();</script>";
        exit();
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    $filePath = "{$userDirectory}/{$username}/info.json";

    if (!file_exists($filePath)) {
        echo "<script>alert('Invalid username or user data not found.');</script>";
        echo "<script>window.location.href='forgot.php';</script>";
        exit();
    }

    $userData = json_decode(file_get_contents($filePath), true);

    if (isset($userData['reset_token']) && $userData['reset_token'] === $token) {

        $userData['password'] = $hashedPassword;
        $userData['reset_token'] = null;

        if (file_put_contents($filePath, json_encode($userData, JSON_PRETTY_PRINT))) {
            echo "<script>alert('Your password has been reset successfully. You can now log in.');</script>";
            echo "<script>window.location.href='login.php';</script>";
            exit();
        } else {
            echo "<script>alert('Failed to save user data. Please try again later.');</script>";
            exit();
        }
    } else {
        echo "<script>alert('Invalid token. Please request a new password reset.');</script>";
        echo "<script>window.location.href='forgot.php';</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

    <form method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
        <input type="hidden" name="username" value="<?php echo htmlspecialchars($_GET['username']); ?>">
    
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>
    
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
