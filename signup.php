<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm-password']));
    if ($password !== $confirm_password) {
        die("<script type='text/javascript'>
                alert('Passwords do not match!');
                window.history.back();
              </script>");
    }
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $dir = "assets/usrdata";
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            die("Failed to create directory: " . $dir);
        }
    }
    $user_dir = $dir . "/" . $user;
    if (is_dir($user_dir)) {
        die("<script type='text/javascript'>
                alert('Username already exists! Please choose another.');
                window.history.back();
              </script>");
    }
    if (!mkdir($user_dir, 0777, true)) {
        die("<script type='text/javascript'>
                alert('Failed to create user folder.');
                window.history.back();
              </script>");
    }
    $info_data = array(
        "username" => $user,
        "email" => $email,
        "password" => $hashed_password,
        "reset_token" => null,
        "update_username" => null,
        "update_password" => null
    );
    $info_json = json_encode($info_data, JSON_PRETTY_PRINT);
    $info_file = $user_dir . "/info.json";
    if (file_put_contents($info_file, $info_json) === false) {
        die("<script type='text/javascript'>
                alert('Failed to create user info file.');
                window.history.back();
              </script>");
    }
    $entries_data = array();
    $entries_json = json_encode($entries_data, JSON_PRETTY_PRINT);
    $entries_file = $user_dir . "/entries.json";
    if (file_put_contents($entries_file, $entries_json) === false) {
        die("<script type='text/javascript'>
                alert('Failed to create entries file.');
                window.history.back();
              </script>");
    }
    echo "<script type='text/javascript'>
            alert('Account successfully created!');
            window.location.href = 'login.php';
          </script>";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-Frame-Options" content="deny">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyJournal - Sign Up</title>
    <link rel="stylesheet" href="assets/css/signup_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="overlay"></div>
    <div class="signup-container">
        <div class="signup-box">
            <h2>Create Your Account</h2>
            <form method="POST" onsubmit="return validatePasswords()">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <i class="fa fa-eye" id="togglePassword1" onclick="togglePasswordVisibility('password', 'togglePassword1')"></i>
                    </div>
                </div>
                <div class="input-group">
                    <label for="confirm-password">Re-enter Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirm-password" name="confirm-password" required>
                        <i class="fa fa-eye" id="togglePassword2" onclick="togglePasswordVisibility('confirm-password', 'togglePassword2')"></i>
                    </div>
                </div>
                <button type="submit" class="signup-button">Sign Up</button>
            </form>
            <div class="links">
                <a href="login.php">Already have an account? Login</a>
            </div>
        </div>
    </div>

    <script src="assets/js/signup.js"></script>
</body>
</html>