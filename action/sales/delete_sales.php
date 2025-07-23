<?php
include '../../db/dbConnection.php';
header('Content-Type: application/json');

function sendResponse($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request.');
}

$sale_id = $_POST['id'] ?? null;

if (empty($sale_id)) {
    sendResponse('error', 'Sale ID is required.');
}

$refillCheck = $conn->prepare("SELECT id FROM refill_history WHERE sales_id = ? AND status = 'Active' LIMIT 1");
$refillCheck->bind_param("i", $sale_id);
$refillCheck->execute();
$refillCheck->store_result();

if ($refillCheck->num_rows > 0) {
    sendResponse('error', 'Active refill history found. Cannot delete.');
}
$refillCheck->close();

// Soft delete by updating status
$updateStmt = $conn->prepare("UPDATE sales SET status = 'Inactive' WHERE id = ?");
$updateStmt->bind_param("i", $sale_id);

if ($updateStmt->execute()) {
    sendResponse('success', 'Sales are deleted successfully.');
} else {
    sendResponse('error', 'Failed to update status.');
}

$updateStmt->close();
$conn->close();
