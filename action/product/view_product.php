<?php
include '../../db/dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("SELECT p.*, c.name AS category_name FROM product p JOIN category c ON c.id = p.cat_id WHERE p.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        echo json_encode([
            'name' => $row['name'],
            'category_name' => $row['category_name'],
            'rate' => $row['rate'],
            'refill_duration' => $row['refill_duration'],
            'description' => $row['description']
        ]);
    } else {
        echo json_encode(null);
    }
}
