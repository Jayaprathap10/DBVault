<?php
session_start();
require 'server.php';

// Ensure the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['submit'])) {
    $userData = mysqli_real_escape_string($db, $_POST['user_data']);
    $password = $_SESSION['password']; // Retrieve the user's password from session
    
    // Derive the encryption key using the password
    $encryptionKey = deriveKey($password);
    
    // Encrypt the user data
    $encryptedData = encryptData($userData, $encryptionKey);

    // Store the encrypted data in the database
    $username = $_SESSION['username'];
    $query = "INSERT INTO user_data (username, encrypted_data) VALUES (?, ?)";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $encryptedData);
    
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
