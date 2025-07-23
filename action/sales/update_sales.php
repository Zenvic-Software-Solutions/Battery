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

// Collect POST values
$edit_sales_id = $_POST['edit_sales_id'] ?? null;
$product_id = $_POST['product_id'] ?? null;
$rate = $_POST['customer_rate'] ?? null;
$customer_name = $_POST['customer_name'] ?? null;
$customer_phone = $_POST['customer_phone'] ?? null;
$customer_address = $_POST['customer_address'] ?? null;
$sale_date = $_POST['date'] ?? null;
$next_refill_date = $_POST['next_refill_date'] ?? null;

if (
    empty($edit_sales_id) || empty($product_id) || empty($rate) || empty($customer_name) ||
    empty($sale_date) || empty($next_refill_date)
) {
    sendResponse('warning', 'Missing required fields.');
}

// Fetch current sale details
$stmt = $conn->prepare("SELECT product_id, sale_date, rate, next_refill_date FROM sales WHERE id = ?");
$stmt->bind_param("i", $edit_sales_id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$current) {
    sendResponse('error', 'Sale record not found.');
}

// Check for active refill history
$check = $conn->prepare("SELECT id FROM refill_history WHERE sales_id = ? AND status = 'Active' LIMIT 1");
$check->bind_param("i", $edit_sales_id);
$check->execute();
$hasActiveRefill = $check->get_result()->num_rows > 0;
$check->close();

if (
    $hasActiveRefill &&
    (
        $current['product_id'] != $product_id ||
        $current['rate'] != $rate ||
        $current['sale_date'] != $sale_date ||
        $current['next_refill_date'] != $next_refill_date
    )
) {
    sendResponse('error', 'Cannot update core fields due to active refill history.');
}

// Proceed with update
$stmt = $conn->prepare("UPDATE sales SET 
    product_id = ?, 
    rate = ?, 
    customer_name = ?, 
    customer_phone = ?, 
    customer_address = ?, 
    sale_date = ?, 
    next_refill_date = ?
    WHERE id = ?");

$stmt->bind_param("idsssssi", $product_id, $rate, $customer_name, $customer_phone, $customer_address, $sale_date, $next_refill_date, $edit_sales_id);

if (!$stmt->execute()) {
    sendResponse('error', 'Failed to update sale.');
}
$stmt->close();

sendResponse('success', 'Sales details updated successfully.');
