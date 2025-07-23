<?php
include '../../db/dbConnection.php';
header('Content-Type: application/json');

function jsonResponse($status, $message) {
    echo json_encode(["status" => $status, "message" => $message]);
    exit;
}

function getClientBalance($conn, $client_id, $client_type) {
    $table = $client_type === 'Retailer' ? 'retailer' : 'customer';
    $stmt = $conn->prepare("SELECT balance FROM $table WHERE id = ? FOR UPDATE");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? (float)$row['balance'] : 0;
}

function getStock($conn, $product_id) {
    $stmt = $conn->prepare("SELECT name, current_stock FROM product WHERE id = ? FOR UPDATE");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? ['stock' => (int)$row['current_stock'], 'name' => $row['name']] : ['stock' => 0, 'name' => 'Unknown Product'];
}

function updateClientBalance($conn, $client_id, $client_type, $balance) {
    $table = $client_type === 'Retailer' ? 'retailer' : 'customer';
    $stmt = $conn->prepare("UPDATE $table SET balance = ? WHERE id = ?");
    $stmt->bind_param("di", $balance, $client_id);
    $stmt->execute();
    $stmt->close();
}

function updateStock($conn, $product_id, $qty_diff) {
    $stmt = $conn->prepare("UPDATE product SET current_stock = current_stock - ? WHERE id = ?");
    $stmt->bind_param("ii", $qty_diff, $product_id);
    $stmt->execute();
    $stmt->close();
}

function deactivateSaleDetail($conn, $sale_id, $product_id) {
    $stmt = $conn->prepare("UPDATE sales_details SET status = 'Inactive' WHERE sales_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $sale_id, $product_id);
    $stmt->execute();
    $stmt->close();
}

function upsertSaleDetail($conn, $sale_id, $product_id, $rate, $qty, $amount, $exists) {
    if ($exists) {
        $stmt = $conn->prepare("UPDATE sales_details SET unit_price = ?, quantity = ?, total_price = ? WHERE sales_id = ? AND product_id = ? AND status = 'Active'");
        $stmt->bind_param("didii", $rate, $qty, $amount, $sale_id, $product_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO sales_details (sales_id, product_id, unit_price, quantity, total_price) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iidid", $sale_id, $product_id, $rate, $qty, $amount);
    }
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode($_POST['invoice'], true);

    if (
        empty($data['sales_id']) || empty($data['client_id']) || empty($data['client_type']) ||
        empty($data['date']) || !isset($data['products']) || !is_array($data['products']) || count($data['products']) === 0
    ) {
        jsonResponse("error", "Missing required fields or no products provided.");
    }

    $sales_id = (int)$data['sales_id'];
    $products = $data['products'];
    $client_id = $data['client_id'];
    $client_type = $data['client_type'];
    $date = $data['date'];
    $gst_percentage = $data['gst_percentage'];
    $discount = $data['discount'];
    $subtotal = $data['subtotal'];
    $discounted_subtotal = $data['discounted_subtotal'];
    $tax = $data['tax'];
    $total = $data['total'];
    $notes = $data['notes'];

    $conn->begin_transaction();

    try {
        $prev = $conn->query("SELECT client_id, client_type, total_amount FROM sales WHERE id = $sales_id AND status = 'Active'")->fetch_assoc();
        $previous_client = (int)$prev['client_id'];
        $previous_type = $prev['client_type'];
        $previous_total = (float)$prev['total_amount'];

        $old_balance = getClientBalance($conn, $previous_client, $previous_type);
        $new_balance = ($previous_client != $client_id || $previous_type !== $client_type)
            ? getClientBalance($conn, $client_id, $client_type)
            : $old_balance;

        // Get existing product details
        $existing = [];
        $res = $conn->query("SELECT product_id, quantity FROM sales_details WHERE sales_id = $sales_id AND status = 'Active'");
        while ($row = $res->fetch_assoc()) {
            $existing[$row['product_id']] = $row['quantity'];
        }

        // Validate stock availability
        foreach ($products as $item) {
            $pid = $item['product_id'];
            $qty = $item['qty'];
            $diff = isset($existing[$pid]) ? ($qty - $existing[$pid]) : $qty;

            if ($diff > 0) {
                $product = getStock($conn, $pid);
                $available = $product['stock'];
                $name = $product['name'];
                if ($available < $diff) {
                    $conn->rollback();
                    jsonResponse("warning", "Not enough stock for '$name'. Only $available available.");
                }
            }
        }

        // Validate balance
        if ($previous_client != $client_id || $previous_type !== $client_type) {
            if ($old_balance < $previous_total) {
                $conn->rollback();
                jsonResponse("warning", "Insufficient balance for the old client.");
            }
        } else {
            if ($old_balance + ($total - $previous_total) < 0) {
                $conn->rollback();
                jsonResponse("warning", "Insufficient balance for the client.");
            }
        }

        // Deactivate removed products
        foreach ($existing as $productId => $oldQty) {
            if (!in_array($productId, array_column($products, 'product_id'))) {
                deactivateSaleDetail($conn, $sales_id, $productId);
                updateStock($conn, $productId, -$oldQty);
            }
        }

        // Update/Add products and stock
        foreach ($products as $item) {
            $pid = $item['product_id'];
            $rate = $item['rate'];
            $qty = $item['qty'];
            $amount = $item['amount'];
            $diff = isset($existing[$pid]) ? ($qty - $existing[$pid]) : $qty;

            upsertSaleDetail($conn, $sales_id, $pid, $rate, $qty, $amount, isset($existing[$pid]));
            if ($diff != 0) updateStock($conn, $pid, $diff);
        }

        // Update balances
        if ($previous_client != $client_id || $previous_type !== $client_type) {
            updateClientBalance($conn, $previous_client, $previous_type, $old_balance - $previous_total);
            updateClientBalance($conn, $client_id, $client_type, $new_balance + $total);
        } else {
            updateClientBalance($conn, $client_id, $client_type, $old_balance + ($total - $previous_total));
        }

        // Update sales table
        $stmt = $conn->prepare("UPDATE sales SET client_id = ?, client_type = ?, date = ?, gst_percentage = ?, discount = ?, sub_total = ?, new_subtotal = ?, gst_amount = ?, total_amount = ?, notes = ? WHERE id = ?");
        $stmt->bind_param("issddddddsi", $client_id, $client_type, $date, $gst_percentage, $discount, $subtotal, $discounted_subtotal, $tax, $total, $notes, $sales_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        jsonResponse("success", "Sales updated successfully!");
    } catch (Exception $e) {
        $conn->rollback();
        jsonResponse("error", "Transaction failed: " . $e->getMessage());
    }
}
