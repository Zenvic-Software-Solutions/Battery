<?php
include '../../db/dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $sale_id = (int)$_POST['id'];

    // Fetch sale main info
    $stmt = $conn->prepare("SELECT * FROM sales WHERE id = ? AND status = 'Active'");
    $stmt->bind_param("i", $sale_id);
    $stmt->execute();
    $saleResult = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$saleResult) {
        echo json_encode([]);
        exit;
    }

    $details = [];
    $detailStmt = $conn->prepare("
        SELECT sd.*, p.cat_id 
        FROM sales_details sd
        JOIN product p ON sd.product_id = p.id
        WHERE sd.sales_id = ? AND sd.status = 'Active'
    ");
    $detailStmt->bind_param("i", $sale_id);
    $detailStmt->execute();
    $result = $detailStmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $details[] = [
            'product_id' => $row['product_id'],
            'category_id' => $row['cat_id'],
            'rate' => $row['unit_price'],
            'qty' => $row['quantity'],
            'amount' => $row['total_price']
        ];
    }

    $detailStmt->close();

    echo json_encode([
        'client_id' => $saleResult['client_id'],
        'client_type' => $saleResult['client_type'],
        'date' => $saleResult['date'],
        'gst_percentage' => $saleResult['gst_percentage'],
        'discount' => $saleResult['discount'],
        'subtotal' => $saleResult['sub_total'],
        'discounted_subtotal' => $saleResult['new_subtotal'],
        'tax' => $saleResult['gst_amount'],
        'total' => $saleResult['total_amount'],
        'notes' => $saleResult['notes'],
        'products' => $details
    ]);
}
?>
