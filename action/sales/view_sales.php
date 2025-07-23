<?php
include '../../db/dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $sales_id = (int)$_POST['id'];

    $stmt = $conn->prepare("
        SELECT s.*, 
               CASE 
                   WHEN s.client_type = 'Customer' THEN cu.name 
                   ELSE r.name 
               END AS client_name
        FROM sales s
        LEFT JOIN customer cu ON s.client_type = 'Customer' AND s.client_id = cu.id
        LEFT JOIN retailer r ON s.client_type = 'Retailer' AND s.client_id = r.id
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
        SELECT 
            sd.product_id,
            sd.unit_price AS rate,
            sd.quantity AS qty,
            sd.total_price AS amount,
            p.name AS product_name,
            p.product_code,
            c.name AS category_name,
            p.cat_id AS category_id
        FROM sales_details sd
        JOIN product p ON sd.product_id = p.id
        JOIN category c ON p.cat_id = c.id
        WHERE sd.sales_id = ? AND sd.status = 'Active'
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $sales_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $details[] = [
            'product_id'     => $row['product_id'],
            'product_name'   => ' <span class="badge bg-secondary-subtle text-secondary fs-6">' . $row['product_code'] . '</span> ' . $row['product_name'],
            'category_id'    => $row['category_id'],
            'category_name'  => $row['category_name'],
            'rate'           => $row['rate'],
            'qty'            => $row['qty'],
            'amount'         => $row['amount']
        ];
    }
    $stmt->close();

    // Return JSON
    echo json_encode([
        'invoice_no'           => $salesResult['invoice_no'],
        'client_id'            => $salesResult['client_id'],
        'client_type'          => $salesResult['client_type'],
        'client_name'          => $salesResult['client_name'],
        'date'                 => date('d M, Y', strtotime($salesResult['date'])),
        'gst_percentage'       => $salesResult['gst_percentage'],
        'discount'             => $salesResult['discount'],
        'subtotal'             => $salesResult['sub_total'],
        'discounted_subtotal'  => $salesResult['new_subtotal'],
        'tax'                  => $salesResult['gst_amount'],
        'total'                => $salesResult['total_amount'],
        'notes'                => $salesResult['notes'],
        'products'             => $details
    ]);
}
?>
