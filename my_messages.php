<?php
// Include the database connection file
include 'db_connection.php';
use Defuse\Crypto\Crypto;

function convertNumericKeyToBinary($numericKey) {
    $binaryKey = str_pad($numericKey, 32, "0", STR_PAD_LEFT); // Pad key with zeros
    return substr($binaryKey, 0, 32); // Ensure it's 32 bytes
}

if (!isset($_GET['mail']) || empty($_GET['mail'])) {
    header("Location: login.php"); // Redirect to login if no email parameter
    exit();
}

$email = htmlspecialchars($_GET['mail']);

// Prepare and execute the query to fetch messages
$stmt = $conn->prepare("SELECT * FROM messages WHERE sender = ? OR receiver = ?");
$stmt->bind_param("ss", $email, $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptChat - My Messages</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0a1f2f; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #1a2a38; 
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 1000px;
            overflow-x: auto; 
        }

        table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 20px;
            table-layout: fixed; 
        }

        table th, table td {
            text-align: left;
            padding: 10px;
            word-wrap: break-word; 
        }

        table th {
            background-color: #007bff; 
            color: white;
        }

        table td {
            background-color: #343a40; 
            color: white;
            overflow: hidden; 
        }

        .btn {
            background-color: #28a745; 
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            padding: 10px;
            margin: 10px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #218838;
        }

        .btn-back {
            background-color: #007bff; 
        }

        .btn-back:hover {
            background-color: #0056b3;
        }

        .error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="color: white;">My Messages</h2>
        <?php if ($result->num_rows > 0) { ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Access Key</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>Status</th>
                        
                        <th>Decrypted Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['access_key']); ?></td>
                            <td><?php echo htmlspecialchars($row['sender']); ?></td>
                            <td><?php echo htmlspecialchars($row['receiver']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            
                            <td>
                                <?php
                                try {
                                    $binaryKey = convertNumericKeyToBinary($row['security_key']);
                                    $ivSize = openssl_cipher_iv_length('aes-256-cbc');
                                    $encryptedText = base64_decode($row['encrypted_text']);
                                    $iv = substr($encryptedText, 0, $ivSize);
                                    $encryptedText = substr($encryptedText, $ivSize);
                                    $decryptedText = openssl_decrypt($encryptedText, 'aes-256-cbc', $binaryKey, 0, $iv);
                                    echo nl2br(htmlspecialchars($decryptedText));
                                } catch (Exception $e) {
                                    echo '<span class="error">Decryption failed: ' . htmlspecialchars($e->getMessage()) . '</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p>No messages found.</p>
        <?php } ?>

        <a href="dashboard.php?mail=<?php echo urlencode($email); ?>" class="btn btn-back">Back to Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
