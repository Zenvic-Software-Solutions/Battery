<?php
include '../../db/dbConnection.php';

$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);
$searchValue = trim($_POST['search']['value'] ?? '');

// Base SELECT with joins
$selectBase = "
    SELECT 
        s.id, s.sale_date, s.rate, s.customer_name, s.customer_phone, s.next_refill_date, s.current_status, p.name, s.notes
    FROM sales s
    LEFT JOIN product p ON p.id = s.product_id
    WHERE s.status = 'Active'
";

// Search filter
$searchSQL = '';
if ($searchValue !== '') {
    $escaped = mysqli_real_escape_string($conn, $searchValue);
    $searchSQL = " AND (
        s.customer_name LIKE '%$escaped%' OR
        s.customer_phone LIKE '%$escaped%' OR
        s.current_status LIKE '%$escaped%' OR
        p.name LIKE '%$escaped%' OR
        DATE_FORMAT(s.sale_date, '%d %b, %Y') LIKE '%$escaped%'
    )";
}

// Total records (no filters)
$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM sales WHERE status = 'Active'");
$total = mysqli_fetch_assoc($totalResult)['total'] ?? 0;

// Filtered records (with search if applied)
$filteredResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM ($selectBase $searchSQL) AS filtered");
$filtered = mysqli_fetch_assoc($filteredResult)['total'] ?? 0;

// Main data query
$dataQuery = mysqli_query($conn, "$selectBase $searchSQL ORDER BY FIELD(s.current_status, 'Active', 'Self-refill', 'Replaced', 'Broken'), s.id DESC LIMIT $start, $length");

$serial = $start + 1;
$data = [];

while ($row = mysqli_fetch_assoc($dataQuery)) {

    $data[] = [
        "serial" => $serial++,
        "sales_date" => '<span class="badge bg-secondary-subtle text-secondary fs-6">' . date('d M, Y', strtotime($row['sale_date'])) . '</span>',
        "name" => htmlspecialchars($row['customer_name']),
        "phone" => htmlspecialchars($row['customer_phone']),
        "product_name" => htmlspecialchars($row['name']),
        "total_amount" => '<span class="text-success fw-bold">₹ ' . indian_number_format($row['rate']) . '</span>',
        "refill_date" => '<span class="badge bg-danger-subtle text-danger fs-6">' . date('d M, Y', strtotime($row['next_refill_date'])) . '</span>',
        "current_status" => '<span class="badge text-capitalize bg-' . match(($row['current_status'])) {
            'Active' => 'success-subtle text-success',
            'Broken' => 'danger-subtle text-danger',
            'Self-refill' => 'warning-subtle text-warning',
            'Replaced' => 'info-subtle text-info',
            default => 'secondary-subtle text-secondary',
        } . ' fs-6">' . htmlspecialchars($row['current_status']) . '</span>',
        "action" => '
            <button class="btn p-0 border-0 bg-transparent me-2" onclick="viewSales(' . $row['id'] . ')" title="View">
                <i class="mdi mdi-eye fs-4 text-success"></i>
            </button>
            <button class="btn p-0 border-0 bg-transparent me-2" onclick="editSales(' . $row['id'] . ')" title="Edit">
                <i class="mdi mdi-square-edit-outline fs-4 text-warning"></i>
            </button>
            <button class="btn p-0 border-0 bg-transparent" onclick="deleteSales(' . $row['id'] . ')" title="Delete">
                <i class="mdi mdi-delete fs-4 text-danger"></i>
            </button>
            <button class="btn p-0 border-0 bg-transparent" onclick="changeStatus(' . $row['id'] . ', \'' . $row['current_status'] . '\', `' . addslashes($row['notes']) . '`)" title="Change Status">
                <i class="mdi mdi-sync fs-4 text-info"></i>
            </button>'
    ];
}

// Indian Number Format
function indian_number_format($num) {
    $x = explode('.', $num);
    $intPart = $x[0];
    $decimalPart = $x[1] ?? '00';
    $lastThree = substr($intPart, -3);
    $restUnits = substr($intPart, 0, -3);
    if ($restUnits != '') {
        $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
        return $restUnits . "," . $lastThree . '.' . str_pad($decimalPart, 2, '0');
    }
    return $lastThree . '.' . str_pad($decimalPart, 2, '0');
}

// Output
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $total,
    "recordsFiltered" => $filtered,
    "data" => $data
]);
?>
