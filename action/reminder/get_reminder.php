<?php
include '../../db/dbConnection.php';

$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);
$searchValue = trim($_POST['search']['value'] ?? '');

// Base SELECT with joins
$selectBase = "
    SELECT 
        s.id, s.customer_name, s.customer_phone, s.customer_address, s.next_refill_date, p.name AS product_name 
    FROM sales s
    LEFT JOIN product p ON p.id = s.product_id
    WHERE s.status = 'Active' AND s.current_status = 'Active' AND s.next_refill_date <= DATE_ADD(CURDATE(), INTERVAL 5 DAY)
";

// Search filter
$searchSQL = '';
if ($searchValue !== '') {
    $escaped = mysqli_real_escape_string($conn, $searchValue);
    $searchSQL = " AND (
        s.customer_name LIKE '%$escaped%' OR
        s.customer_phone LIKE '%$escaped%' OR
        s.customer_address LIKE '%$escaped%' OR
        p.name LIKE '%$escaped%' 
    )";
}

// Total records (no filters)
$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM sales WHERE status = 'Active' AND current_status = 'Active'");
$total = mysqli_fetch_assoc($totalResult)['total'] ?? 0;

// Filtered records (with search if applied)
$filteredResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM ($selectBase $searchSQL) AS filtered");
$filtered = mysqli_fetch_assoc($filteredResult)['total'] ?? 0;

// Main data query
$dataQuery = mysqli_query($conn, "$selectBase $searchSQL ORDER BY s.next_refill_date LIMIT $start, $length");

$serial = $start + 1;
$data = [];

while ($row = mysqli_fetch_assoc($dataQuery)) {

    $diffDays = floor((strtotime($row['next_refill_date']) - strtotime(date('Y-m-d'))) / (60 * 60 * 24));
    if ($diffDays == 0) {
        $daysLeftText = '<span class="badge bg-success-subtle text-success fs-6">Today</span>';
    } elseif ($diffDays > 0) {
        $daysLeftText = '<span class="badge bg-secondary-subtle text-secondary fs-6">' . $diffDays . ' days to go</span>';
    } else {
        $daysLeftText = '<span class="badge bg-danger-subtle text-danger fs-6">' . abs($diffDays) . ' days ago</span>';
    }

    $data[] = [
        "serial" => $serial++,
        "client_name" => htmlspecialchars($row['customer_name']),
        "client_phone" => htmlspecialchars($row['customer_phone']),
        "client_address" => htmlspecialchars($row['customer_address']),
        "days_left" => $daysLeftText,
        "product_name" => htmlspecialchars($row['product_name']),
        "action" => '
            <button class="btn border-0 p-0 bg-success-subtle" onclick="openRefillModal(' . $row['id'] . ')" title="Refill Battery">
                <i class="mdi mdi-battery-charging fs-4 text-primary"></i>
            </button>'
    ];
}

// Output
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $total,
    "recordsFiltered" => $filtered,
    "data" => $data
]);
?>
