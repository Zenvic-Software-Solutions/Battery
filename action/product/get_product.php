<?php
include '../../db/dbConnection.php';

$draw = $_POST['draw'];
$start = $_POST['start'];
$length = $_POST['length'];
$searchValue = $_POST['search']['value'] ?? '';

// Total records
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM product WHERE status='Active'");
$total = mysqli_fetch_assoc($totalQuery)['total'];

// Search
$searchSQL = "";
if (!empty($searchValue)) {
    $searchValue = mysqli_real_escape_string($conn, $searchValue);
    $searchSQL = " AND (
        p.name LIKE '%$searchValue%' OR
        c.name LIKE '%$searchValue%'
    )";
}

// Total after filter
$filterQuery = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM product p
    LEFT JOIN category c ON c.id = p.cat_id
    WHERE p.status='Active' $searchSQL
");
$filtered = mysqli_fetch_assoc($filterQuery)['total'];

// Main data query
$query = mysqli_query($conn, "
    SELECT p.id,  p.name, p.rate, p.refill_duration, c.name AS category_name
    FROM product p
    LEFT JOIN category c ON c.id = p.cat_id
    WHERE p.status='Active' $searchSQL
    ORDER BY p.id DESC
    LIMIT $start, $length
");

$serial = $start + 1;
$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = [
        "serial" => $serial++,
        "id" => $row['id'],
        "name" => htmlspecialchars($row['name']),
        "category_name" => htmlspecialchars($row['category_name']),
        "customer_rate" => "<span class='text-success fw-bold'>₹ " . number_format($row['rate'], 2) . "</span>",
        "refill_duration" => "<span class='text-danger fw-bold'>" . $row['refill_duration'] . " days</span>",
        "action" => '
            <button class="btn p-0 border-0 bg-transparent me-2"
                    onclick="viewProduct(' . $row['id'] . ')"
                    title="View">
                <i class="mdi mdi-eye fs-4 text-success"></i>
            </button>
            <button class="btn p-0 border-0 bg-transparent me-2"
                    onclick="editProduct(' . $row['id'] . ')"
                    title="Edit">
                <i class="mdi mdi-folder-edit fs-4 text-warning"></i>
            </button>
            <button class="btn p-0 border-0 bg-transparent"
                    onclick="deleteProduct(' . $row['id'] . ')"
                    title="Delete">
                <i class="mdi mdi-delete fs-4 text-danger"></i>
            </button>'
    ];
}

// Output JSON
echo json_encode([
    "draw" => intval($draw),
    "recordsTotal" => intval($total),
    "recordsFiltered" => intval($filtered),
    "data" => $data
]);
