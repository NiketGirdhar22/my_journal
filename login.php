<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameInput = htmlspecialchars(trim($_POST['username']));
    $passwordInput = htmlspecialchars(trim($_POST['password']));
    $dir = "assets/usrdata";
    $user_dir = $dir . "/" . $usernameInput;
    $info_file = $user_dir . "/info.json";
    if (is_dir($user_dir) && file_exists($info_file)) {
        $user_data = json_decode(file_get_contents($info_file), true);

        if (password_verify($passwordInput, $user_data['password'])) {
            session_start();
            $_SESSION['username'] = $user_data['username'];
            header("Location: welcomeback.php");
            exit();
        } else {
            echo "<script type='text/javascript'>
                    alert('Invalid password. Please try again.');
                    window.location.href = 'login.php';
                  </script>";
        }
    } else {
        echo "<script type='text/javascript'>
                alert('No account found with that username. Press OK to make a new account.');
                window.location.href = 'signup.php';
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
            <h2>Login to MyJournal</h2>
            <form method="POST">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-button">Login</button>
                <div class="links">
                    <a href="forgot.php">Forgot Password?</a>
                    <a href="signup.php">Sign Up</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>