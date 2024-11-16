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

    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm-password']);

    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO user (username, email, password) VALUES ('$user', '$email', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        $user_data = array(
            "username" => $user,
            "email" => $email,
            "journal_entries" => []
        );

        $json_data = json_encode($user_data, JSON_PRETTY_PRINT);

        $dir = "usrdata";
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                die("Failed to create directory: " . $dir);
            }
        }

        $file_path = $dir . "/" . $user . ".json";
        if (file_put_contents($file_path, $json_data) === false) {
            die("Failed to create JSON file at " . $file_path);
        }

        echo "<script type='text/javascript'>
                alert('Account successfully created!');
                window.location.href = '../../login.html';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
