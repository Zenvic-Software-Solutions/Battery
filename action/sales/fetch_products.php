<?php
include '../../db/dbConnection.php';
$category_id = $_POST['category_id'] ?? '';
$data = [];

if ($category_id !== '') {
    $stmt = $conn->prepare("SELECT id, name, rate, refill_duration FROM product WHERE cat_id = ? AND status = 'Active'");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
