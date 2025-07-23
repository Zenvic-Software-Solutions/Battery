<?php
include '../../db/dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $sale_id = (int)$_POST['id'];

    // Fetch sale main info
    $stmt = $conn->prepare("SELECT a.*, b.cat_id FROM sales a LEFT JOIN product b ON a.product_id = b.id WHERE a.id = ? AND a.status = 'Active'");
    $stmt->bind_param("i", $sale_id);
    $stmt->execute();
    $saleResult = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$saleResult) {
        echo json_encode([]);
        exit;
    }

    echo json_encode([
        'id' => $saleResult['id'],
        'refill_date' => $saleResult['next_refill_date'],
        'date' => $saleResult['sale_date'],
        'category_id' => $saleResult['cat_id'],
        'product_id' => $saleResult['product_id'],
        'customer_rate' => $saleResult['rate'],
        'customer_name' => $saleResult['customer_name'],
        'customer_phone' => $saleResult['customer_phone'],
        'customer_address' => $saleResult['customer_address'],
    ]);
}
?>
