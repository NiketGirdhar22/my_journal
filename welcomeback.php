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

// Initialize entries array and selected entry variable
$entries = [];
$selectedEntry = null;

// Check if info.json exists to retrieve the encryption key
if (!file_exists($infoFilePath)) {
    die("Info file (info.json) does not exist.");
}

$infoData = json_decode(file_get_contents($infoFilePath), true);
if (!isset($infoData['encryptionKey'])) {
    die("Encryption key is missing in info.json.");
}

$encryptionKey = $infoData['encryptionKey'];  // Retrieve encryption key from info.json
$encryptionMethod = 'AES-256-CBC';  // The encryption method used

// Function to decrypt data
function decrypt($data, $key, $method) {
    list($encryptedData, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encryptedData, $method, $key, 0, $iv);
}

// Function to encrypt data
function encrypt($data, $key, $method) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method)); // Generate a random IV
    $encryptedData = openssl_encrypt($data, $method, $key, 0, $iv);
    // Return the encrypted data with the IV (base64 encoded)
    return base64_encode($encryptedData . '::' . $iv);
}

// Load and decrypt entries if entries file exists
if (is_dir($userDir) && file_exists($entriesFilePath)) {
    $entriesData = file_get_contents($entriesFilePath);
    $entries = json_decode($entriesData, true) ?: [];
}

// If an entry index is passed via GET, fetch and decrypt the selected entry
if (isset($_GET['entry'])) {
    $entryIndex = (int) $_GET['entry'];
    if (isset($entries[$entryIndex])) {
        $selectedEntry = $entries[$entryIndex];

        // Decrypt title and content before displaying
        $selectedEntry['title'] = decrypt($selectedEntry['title'], $encryptionKey, $encryptionMethod);
        $selectedEntry['content'] = decrypt($selectedEntry['content'], $encryptionKey, $encryptionMethod);
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
                                    <?php 
                                    // Decrypt the title for display in the entries list
                                    $decryptedTitle = decrypt($entry['title'], $encryptionKey, $encryptionMethod);
                                    echo htmlspecialchars($decryptedTitle); 
                                    ?>
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
                        <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this entry? This action cannot be undone.');">Delete Entry</button>
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
