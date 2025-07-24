<?php
session_start();
include "db/dbConnection.php";

$user_id = $_SESSION['user_id'] ?? 1;

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['endpoint'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid subscription data']);
    exit;
}

$endpoint = $data['endpoint'];
$p256dh = $data['keys']['p256dh'];
$auth = $data['keys']['auth'];

// Check if subscription already exists
$sql = "SELECT id FROM push_subscriptions WHERE endpoint = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $endpoint);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    // Insert new subscription
    $sql = "INSERT INTO push_subscriptions (user_id, endpoint, public_key, auth_token) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $endpoint, $p256dh, $auth);
    $stmt->execute();
}

echo json_encode(['success' => true]);
?>
