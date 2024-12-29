<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usernameInput = htmlspecialchars(trim($_POST['username']));
    if (empty($usernameInput)) {
        echo "<script>alert('Please enter your username.'); window.history.back();</script>";
        exit();
    }

    $userDirectory = "assets/usrdata";
    $userFound = false;

    $jsonFilePath = "{$userDirectory}/{$usernameInput}/info.json";

    if (file_exists($jsonFilePath)) {
        $userData = json_decode(file_get_contents($jsonFilePath), true);

        $userFound = true;
        $resetToken = bin2hex(random_bytes(32));
        $userData['reset_token'] = $resetToken;

        if (file_put_contents($jsonFilePath, json_encode($userData, JSON_PRETTY_PRINT))) {
            $resetLink = "http://localhost/my_journal/reset.php?token={$resetToken}&username={$usernameInput}";

            $subject = "Password Reset Instructions";
            $message = "Hello {$usernameInput},\n\nClick the following link to reset your password: {$resetLink}\n\nIf you didn't request this, please ignore this email.";
            $headers = "From: no-reply@myjournal.com";

            if (mail($userData['email'], $subject, $message, $headers)) {
                echo "<script>alert('Password reset instructions have been sent to your email.'); window.location.href = 'login.php';</script>";
            } else {
                echo "<script>alert('Failed to send email. Please try again later.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Failed to update user data. Please try again later.'); window.history.back();</script>";
        }
    }

    if (!$userFound) {
        echo "<script>alert('No account found with that username.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyJournal</title>
    <link rel="stylesheet" href="assets/css/forgot_styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <div class="overlay"></div>
    <div class="forgot-password-container">
        <div class="forgot-password-box">
            <h2>Forgot Your Password?</h2>
            <p>Enter your username, and we'll mail you instructions to reset your password.</p>
            
            <form method="POST">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <button type="submit" class="reset-button">Send Reset Instructions</button>
            </form>

            <div class="links">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
