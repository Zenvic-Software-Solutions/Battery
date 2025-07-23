<?php
include '../../db/dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $sales_id = (int)$_POST['id'];

    $stmt = $conn->prepare("
        SELECT s.*, 
               p.name AS product_name,
               c.name AS category_name 
        FROM sales s
        LEFT JOIN product p ON s.product_id = p.id
        LEFT JOIN category c ON p.cat_id = c.id
        WHERE s.id = ? AND s.status = 'Active'
    ");
    $stmt->bind_param("i", $sales_id);
    $stmt->execute();
    $salesResult = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$salesResult) {
        echo json_encode([]);
        exit;
    }

    $details = [];
    $query = "
        SELECT id, refill_date, amount, notes
        FROM refill_history
        WHERE sales_id = ? AND status = 'Active'
        ORDER BY refill_date DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $sales_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $details[] = [
            'refill_id'     => $row['id'],
            'refill_date'   => date('d M, Y', strtotime($row['refill_date'])),
            'amount'        => $row['amount'],
            'notes'         => $row['notes']
        ];
    }
    $stmt->close();

    // Return JSON
    echo json_encode([
        'product'           => $salesResult['product_name'],
        'category'          => $salesResult['category_name'],
        'sales_date'        => date('d M, Y', strtotime($salesResult['sale_date'])),
        'refill_date'       => date('d M, Y', strtotime($salesResult['next_refill_date'])),
        'customer_name'     => $salesResult['customer_name'],
        'customer_phone'    => $salesResult['customer_phone'],
        'customer_address'  => $salesResult['customer_address'],
        'customer_rate'     => $salesResult['rate'],
        'current_status'    => $salesResult['current_status'],
        'status_notes'      => $salesResult['notes'],
        'refill'            => $details
    ]);
}
?>
