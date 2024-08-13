<?php


// Initializing variables
$username = "";
$email    = "";
$errors = array(); 

// Connect to the database
$db = mysqli_connect('localhost', 'root', '', 'project');

if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Function to generate a random encryption key
function generateEncryptionKey() {
    return openssl_random_pseudo_bytes(32); // 32 bytes = 256 bits
}

// Function to encrypt data using AES-256-CBC
function encryptData($data, $key) {
    $iv_length = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($iv_length);
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    // Store IV with encrypted data for decryption
    return base64_encode($encrypted . '::' . $iv);
}

// Function to decrypt data using AES-256-CBC
function decryptData($data, $key) {
    $data = base64_decode($data);
    list($encrypted_data, $iv) = explode('::', $data, 2);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
}

// REGISTER USER
if (isset($_POST['reg_user'])) {
    // Receive all input values from the form
    $username   = mysqli_real_escape_string($db, $_POST['username']);
    $email      = mysqli_real_escape_string($db, $_POST['email']);
    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

    // Form validation: ensure that the form is correctly filled
    if (empty($username))  { array_push($errors, "Username is required"); }
    if (empty($email))     { array_push($errors, "Email is required"); }
    if (empty($password_1)){ array_push($errors, "Password is required"); }
    if ($password_1 != $password_2) {
        array_push($errors, "The two passwords do not match");
    }

    // Check the database to make sure a user does not already exist with the same username and/or email
    $user_check_query = "SELECT * FROM users WHERE username=? OR email=? LIMIT 1";
    $stmt = mysqli_prepare($db, $user_check_query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
  
    if ($user) { // If user exists
        if ($user['username'] === $username) {
            array_push($errors, "Username already exists");
        }

        if ($user['email'] === $email) {
            array_push($errors, "Email already exists");
        }
    }

    // Finally, register user if there are no errors in the form
    if (count($errors) == 0) {
        // Use password_hash instead of md5 for better security
        $password = password_hash($password_1, PASSWORD_DEFAULT); // Encrypt the password before saving in the database

        $query = "INSERT INTO users (username, email, password) VALUES(?, ?, ?)";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['username'] = $username;
            header('location: index.php');
            exit();
        } else {
            array_push($errors, "Failed to register user");
        }
        mysqli_stmt_close($stmt);
    }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if (empty($username)) { array_push($errors, "Username is required"); }
    if (empty($password)) { array_push($errors, "Password is required"); }

    if (count($errors) == 0) {
        $query = "SELECT * FROM users WHERE username=? LIMIT 1";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $username;
                header('location: index.php');
                exit();
            } else {
                array_push($errors, "Wrong username/password combination");
            }
        } else {
            array_push($errors, "Wrong username/password combination");
        }
        mysqli_stmt_close($stmt);
    }
}
?>
