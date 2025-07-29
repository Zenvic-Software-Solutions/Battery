<?php
include '../../db/dbConnection.php';

$id = intval($_POST['id']);
$name = trim($_POST['name']);
$catId = intval($_POST['category_id']);
$customerRate = $_POST['customer_rate'];
$reminder = $_POST['reminder_status'];
$refillDuration = intval($_POST['refill_duration']);
$description = trim($_POST['description']);

// Check duplicates: Product name in category
$checkName = $conn->prepare("SELECT id FROM product WHERE name = ? AND cat_id = ? AND id != ? AND status = 'Active'");
$checkName->bind_param("sii", $name, $catId, $id);
$checkName->execute();
$checkName->store_result();
if ($checkName->num_rows > 0) {
    echo "Product name already exists in this category.";
    exit;
}

// Update query
    $update = $conn->prepare("UPDATE product SET name = ?, cat_id = ?, rate = ?, reminder_status = ?, refill_duration = ?, description = ? WHERE id = ?");
    $update->bind_param("siisisi", $name, $catId, $customerRate, $reminder, $refillDuration, $description, $id);


if ($update->execute()) {
    echo "Product updated successfully.";
} else {
    echo "Failed to update product.";
}
