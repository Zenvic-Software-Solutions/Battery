<?php
include '../../db/dbConnection.php';
header('Content-Type: application/json');

function sendResponse($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

$sale_id = $_POST['saleId'] ?? '';
$status = $_POST['sales_status'] ?? '';
$notes = $_POST['sales_notes'] ?? '';

if (!$sale_id || !$status) {
    sendResponse('error', 'Missing required fields.');
}

$stmt = $conn->prepare("UPDATE sales SET current_status = ?, notes = ? WHERE id = ?");
$stmt->bind_param("ssi", $status, $notes, $sale_id);

if ($stmt->execute()) {
    sendResponse('success', 'Status updated successfully.');
} else {
    sendResponse('error', 'Failed to update status.');
}
