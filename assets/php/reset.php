<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "my_journal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$token = isset($_POST['token']) ? mysqli_real_escape_string($conn, $_POST['token']) : '';

if (empty($token)) {
    echo "<script>alert('No token found.');</script>";
    echo "<script>window.location.href='../../forgot.html';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
        exit();
    }

    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("SELECT reset_token FROM user WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt_update = $conn->prepare("UPDATE user SET password = ?, reset_token = NULL WHERE reset_token = ?");
        $stmt_update->bind_param("ss", $hashed_password, $token);

        if ($stmt_update->execute()) {
            echo "<script>alert('Your password has been reset successfully. You can now log in.'); window.location.href='login.html';</script>";
        } else {
            echo "<script>alert('Failed to reset password. Please try again later.');</script>";
        }

        $stmt_update->close();
    } else {
        echo "<script>alert('Invalid token. Please request a new password reset.');</script>";
        echo "<script>window.location.href='../../forgot.html';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
