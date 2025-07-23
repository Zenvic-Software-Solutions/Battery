<?php
include "../../db/dbConnection.php";

$data = [];
$sql = "SELECT id, name FROM category WHERE status='Active'";
$result = mysqli_query($conn, $sql);
$i = 1;

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        $i++,
        htmlspecialchars($row['name']),
        '<button type="button" class="btn p-0 border-0 bg-transparent editBtn me-2"
            data-id="' . $row['id'] . '" 
            data-name="' . htmlspecialchars($row['name']) . '" 
            title="Edit">
            <i class="mdi mdi-folder-edit fs-4 text-warning"></i>
        </button>
        <button type="button" class="btn p-0 border-0 bg-transparent deleteBtn" 
            data-id="' . $row['id'] . '" 
            title="Delete">
            <i class="mdi mdi-delete fs-4 text-danger"></i>
        </button>'
    ];
}

echo json_encode(["data" => $data]);
