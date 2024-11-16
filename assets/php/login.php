<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "my_journal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    $usernameInput = mysqli_real_escape_string($conn, $_POST['username']);
    $passwordInput = mysqli_real_escape_string($conn, $_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $usernameInput);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($passwordInput, $row['password'])) {
            session_start();
            $_SESSION['username'] = $row['username'];
            header("Location: ../../welcomeback.php");
            exit();
        } else {
            echo "<script type='text/javascript'>
                alert('Invalid password. Please try again.');
                window.location.href = '../../login.html';
              </script>";
        }
    } else {
        echo "<script type='text/javascript'>
            alert('No account found with that username. Press OK to make a new account.');
            window.location.href = '../../signup.html';
          </script>";
    }
}

$stmt->close();
$conn->close();
?>