<?php
include '../../db/dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $sales_id = (int)$_POST['id'];

    $conn->begin_transaction();

    try {
        // Get sales info
        $salesResult = $conn->query("SELECT client_type, client_id, total_amount FROM sales WHERE id = $sales_id AND status = 'Active'");
        if ($salesResult->num_rows === 0) {
            echo "Sale not found or already inactive.";
            exit;
        }

        $salesData = $salesResult->fetch_assoc();
        $client_type = $salesData['client_type'];
        $client_id = (int)$salesData['client_id'];
        $total_amount = (float)$salesData['total_amount'];

        $table = ($client_type === 'Customer') ? 'customer' : 'retailer';

        // Check client balance
        $clientResult = $conn->query("SELECT balance FROM $table WHERE id = $client_id");
        $clientData = $clientResult->fetch_assoc();
        $current_balance = (float)$clientData['balance'];

        if ($current_balance < $total_amount) {
            $conn->rollback();
            echo "Failed to delete sale. Client balance ₹" . number_format($current_balance, 2) . " is less than sale total.";
            exit;
        }

        // Check and prepare stock reversal
        $result = $conn->query("
            SELECT sd.product_id, sd.quantity, p.current_stock
            FROM sales_details sd
            LEFT JOIN product p ON sd.product_id = p.id
            WHERE sd.sales_id = $sales_id AND sd.status = 'Active'
        ");

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        // Increase product stock
        $stmt = $conn->prepare("UPDATE product SET current_stock = current_stock + ? WHERE id = ?");
        foreach ($products as $item) {
            $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
            $stmt->execute();
        }
        $stmt->close();

        // Inactivate sales and sales_details
        $conn->query("UPDATE sales_details SET status = 'Inactive' WHERE sales_id = $sales_id");
        $conn->query("UPDATE sales SET status = 'Inactive' WHERE id = $sales_id");

        // Update client balance
        $updateBalance = $conn->prepare("UPDATE $table SET balance = balance - ? WHERE id = ?");
        $updateBalance->bind_param("di", $total_amount, $client_id);
        $updateBalance->execute();
        $updateBalance->close();

        $conn->commit();
        echo "Sale deleted successfully.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Failed to delete sale.";
    }
} else {
    echo "Invalid request.";
}
