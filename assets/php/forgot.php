<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "my_journal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $reset_token = bin2hex(random_bytes(32));
        $stmt_update = $conn->prepare("UPDATE user SET reset_token = ? WHERE email = ?");
        $stmt_update->bind_param("ss", $reset_token, $email);
        $stmt_update->execute();
        $reset_link = "http://localhost/my_journal/reset.html?token=" . $reset_token;
        $subject = "Password Reset Instructions";
        $message = "Click the following link to reset your password: " . $reset_link . "\nPlease use this link to reset your password.";
        $headers = "From: no-reply@myjournal.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "<script>alert('Password reset instructions have been sent to your email.');</script>";
        } else {
            echo "<script>alert('Failed to send email. Please try again later.');</script>";
        }

        $stmt_update->close();
    } else {
        echo "<script>alert('No account found with that email address.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
