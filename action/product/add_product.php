<?php
include '../../db/dbConnection.php';

$name = trim($_POST['name']);
$catId = intval($_POST['category_id']);
$customerRate = $_POST['customer_rate'];
$refillDuration = intval($_POST['refill_duration']);
$description = trim($_POST['description']);
$imagePath = '';

// Check for duplicate product name in same category
$checkName = $conn->prepare("SELECT id FROM product WHERE name = ? AND cat_id = ? AND status = 'Active'");
$checkName->bind_param("si", $name, $catId);
$checkName->execute();
$checkName->store_result();
if ($checkName->num_rows > 0) {
    echo "Product name already exists in this category.";
    exit;
}

// Insert the product
$insert = $conn->prepare("INSERT INTO product (name, cat_id, rate, refill_duration, description)
                          VALUES (?, ?, ?, ?, ?)");
$insert->bind_param("siiis", $name, $catId, $customerRate, $refillDuration, $description);

if ($insert->execute()) {
    echo "Product added successfully.";
} else {
    echo "Failed to add product.";
}
