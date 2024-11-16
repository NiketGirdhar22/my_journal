<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$username = $_SESSION['username'];
$jsonFilePath = "assets/php/usrdata/{$username}.json";

$entries = [];
$selectedEntry = null;

if (file_exists($jsonFilePath)) {
    $jsonData = file_get_contents($jsonFilePath);
    $data = json_decode($jsonData, true);
    $entries = $data['entries'] ?? [];
}

if (isset($_GET['entry'])) {
    $entryIndex = (int) $_GET['entry'];
    if (isset($entries[$entryIndex])) {
        $selectedEntry = $entries[$entryIndex];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Back - MyJournal</title>
    <link rel="stylesheet" href="assets/css/welcomeback_styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="nav-bar">
        <a href="#" class="nav-logo">MyJournal</a>
        <div class="nav-links">
            <a href="assets/php/logout.php">Logout</a>
        </div>
    </div>

    <div class="main-section">
        <div class="welcome-message">
            <h1>Welcome back, <?php echo htmlspecialchars($username); ?></h1>
        </div>
        <div class="journal-container">
            <div class="entries-column">
                <h2>Previous Entries</h2>
                <ul class="entries-list">
                    <?php if (count($entries) > 0): ?>
                        <?php foreach ($entries as $index => $entry): ?>
                            <li>
                                <a href="?entry=<?php echo $index; ?>">
                                    <strong><?php echo htmlspecialchars($entry['date']); ?></strong> - 
                                    <?php echo htmlspecialchars($entry['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No entries found.</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="entry-area">
                <form action="assets/php/save_entry.php" method="POST">
                    <input 
                        type="text" 
                        id="entryTitle" 
                        name="entryTitle" 
                        placeholder="Title of your entry" 
                        value="<?php echo htmlspecialchars($selectedEntry['title'] ?? ''); ?>" 
                        required
                    >
                    <textarea 
                        id="entryContent" 
                        name="entryContent" 
                        placeholder="Write your journal entry here..." 
                        rows="15" 
                        required
                    ><?php echo htmlspecialchars($selectedEntry['content'] ?? ''); ?></textarea>
                    <button type="submit">Submit Entry</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
