<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$userDir = "assets/usrdata/{$username}";
$infoFilePath = "{$userDir}/info.json";
$entriesFilePath = "{$userDir}/entries.json";

$entries = [];
$selectedEntry = null;

if (is_dir($userDir) && file_exists($entriesFilePath)) {
    $entriesData = file_get_contents($entriesFilePath);
    $entries = json_decode($entriesData, true) ?: [];
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
    <nav class="nav-bar">
        <a href="#" class="nav-logo">MyJournal</a>
        <div class="hamburger" id="hamburger">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <div class="nav-links" id="nav-links">
            <a href="update_username.php">Change Username</a>
            <a href="update_password.php">Change Password</a>
            <a href="delete_account.php" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">Delete Account</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="main-section">
        <div class="welcome-message">
            <h1>Welcome back, <?php echo htmlspecialchars($username); ?></h1>
        </div>
        <div class="journal-container">
            <div class="entries-column">
                <h2>Previous Entries</h2>
                <ul class="entries-list">
                    <?php if (!empty($entries)): ?>
                        <?php foreach ($entries as $index => $entry): ?>
                            <li>
                                <a href="?entry=<?php echo $index; ?>">
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
                <form action="save_entry.php" method="POST">
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
                    
                    <?php if ($selectedEntry): ?>
                        <input type="hidden" name="entryIndex" value="<?php echo $entryIndex; ?>">
                        <button type="submit" name="action" value="update">Update Entry</button>
                        <button type="button" onclick="window.location.href='welcomeback.php'">Start New Entry</button>
                    <?php else: ?>
                        <button type="submit" name="action" value="save">Save Entry</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('hamburger').addEventListener('click', function() {
            document.getElementById('nav-links').classList.toggle('open');
            this.classList.toggle('open');
        });
    </script>
</body>
</html>
