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

// Collect POST values directly
$product_id = $_POST['product_id'] ?? null;
$rate = $_POST['customer_rate'] ?? null;
$customer_name = $_POST['customer_name'] ?? null;
$customer_phone = $_POST['customer_phone'] ?? null;
$customer_address = $_POST['customer_address'] ?? null;
$sale_date = $_POST['date'] ?? null;
$next_refill_date = $_POST['next_refill_date'] ?? null;

if (
    empty($product_id) || empty($rate) || empty($customer_name) ||
    empty($sale_date) || empty($next_refill_date)
) {
    sendResponse('warning', 'Missing required fields.');
}

// Insert into sales
$stmt = $conn->prepare("INSERT INTO sales 
    (product_id, rate, customer_name, customer_phone, customer_address, sale_date, next_refill_date) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("idsssss", $product_id, $rate, $customer_name, $customer_phone, $customer_address, $sale_date, $next_refill_date);

if (!$stmt->execute()) {
    sendResponse('error', 'Failed to insert sale.');
}
$stmt->close();

sendResponse('success', 'Sale added successfully.');
