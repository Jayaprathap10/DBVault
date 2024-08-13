<?php
session_start();
require 'server.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];

// Fetch the encrypted data from the database for the logged-in user
$query = "SELECT encrypted_data, encryption_key FROM user_data WHERE username=?";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$dataList = [];

while ($row = mysqli_fetch_assoc($result)) {
    $encryptedData = $row['encrypted_data'];
    $encryptionKey = base64_decode($row['encryption_key']);
    $decryptedData = decryptData($encryptedData, $encryptionKey);
    $dataList[] = $decryptedData;
}

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <a href="add.php">Add Data</a>
    
    <h3>Your Data:</h3>
    <?php if (empty($dataList)): ?>
        <p>No data found. Click the "Add Data" button to add some data.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($dataList as $data): ?>
                <li><?php echo htmlspecialchars($data); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <form action="logout.php" method="post">
        <button type="submit" name="logout">Logout</button>
    </form>
</body>
</html>
