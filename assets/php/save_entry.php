<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    $title = $_POST['entryTitle'];
    $content = $_POST['entryContent'];
    $date = date("Y-m-d H:i:s");

    $jsonFilePath = "usrdata/{$username}.json";

    if (!file_exists($jsonFilePath)) {
        $data = ['entries' => []];
    } else {
        $jsonData = file_get_contents($jsonFilePath);
        $data = json_decode($jsonData, true);
    }

    $newEntry = [
        'date' => $date,
        'title' => $title,
        'content' => $content
    ];

    $data['entries'][] = $newEntry;

    file_put_contents($jsonFilePath, json_encode($data, JSON_PRETTY_PRINT));

    echo "<script>alert('Entry saved successfully!'); window.location.href='../../welcomeback.php';</script>";
} else {
    header("Location: ../../welcomeback.php");
    exit();
}
?>
