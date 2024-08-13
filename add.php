<?php
session_start();
require 'server.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['submit'])) {
    $userData = mysqli_real_escape_string($db, $_POST['user_data']);
    
    // Generate a dynamic encryption key
    $encryptionKey = generateEncryptionKey();
    
    // Encrypt the user data using the dynamic key
    $encryptedData = encryptData($userData, $encryptionKey);

    // Store the encrypted data and the key in the database
    $username = $_SESSION['username']; // Assuming username is stored in session
    $keyBase64 = base64_encode($encryptionKey); // Store the key as a base64 encoded string
    $query = "INSERT INTO user_data (username, encrypted_data, encryption_key) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "sss", $username, $encryptedData, $keyBase64);
    
    if (mysqli_stmt_execute($stmt)) {
        // Redirect to homepage after successful submission
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Data</title>
</head>
<body>
    <h2>Enter Your Data</h2>
    <form action="add.php" method="post">
        <input type="text" name="user_data" placeholder="Enter your Data" required>
        <button type="submit" name="submit">Submit</button>
    </form>
</body>
</html>
