<?php
session_start();
include 'db/dbConnection.php'; 

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    $_SESSION['error'] = "Please enter username and password.";
    header("Location: index.php");
    exit();
}

// Query to check user credentials
$sql = "SELECT id, username, password, role, name FROM user WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Use password_hash for secure passwords
    if ($password === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Incorrect password.";
    }
} else {
    $_SESSION['error'] = "User not found.";
}

header("Location: index.php");
exit();
?>
