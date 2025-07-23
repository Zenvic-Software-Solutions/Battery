<?php
include '../../db/dbConnection.php';
header('Content-Type: application/json');

function sendResponse($status, $message, $extra = []) {
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
}

// Get POST values
$reminder_id = $_POST['reminder_id'] ?? null;
$refill_date = $_POST['refill_date'] ?? null;
$refill_amount = $_POST['refill_amount'] ?? null;
$notes = $_POST['notes'] ?? null;

if (!$reminder_id || !$refill_date || !$refill_amount) {
    sendResponse('warning', 'All required fields must be filled.');
}

// Fetch refill duration from joined sales and product
$stmt = $conn->prepare("
    SELECT p.refill_duration 
    FROM sales s 
    JOIN product p ON s.product_id = p.id 
    WHERE s.id = ?
");
$stmt->bind_param("i", $reminder_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows === 0) {
    sendResponse('error', 'Sale or product not found.');
}

$row = $result->fetch_assoc();
$refill_days = (int)$row['refill_duration'];
$next_refill_date = date('Y-m-d', strtotime("$refill_date +$refill_days days"));

// Update next refill date in sales
$update = $conn->prepare("UPDATE sales SET next_refill_date = ? WHERE id = ?");
$update->bind_param("si", $next_refill_date, $reminder_id);
if (!$update->execute()) {
    sendResponse('error', 'Failed to update sales.');
}
$update->close();

$insert = $conn->prepare("
    INSERT INTO refill_history (sales_id, refill_date, amount, notes)
    VALUES (?, ?, ?, ?)
");
$insert->bind_param("isis", $reminder_id, $refill_date, $refill_amount, $notes);
if (!$insert->execute()) {
    sendResponse('error', 'Failed to insert refill history.');
}
$insert->close();

sendResponse('success', 'Refill updated successfully.');
