<?php
include "db/dbConnection.php";

// Fetch Today Refills
$todayRefills = 0;
$sql = "SELECT COUNT(*) as total FROM sales WHERE DATE(next_refill_date) = CURDATE() AND status = 'Active' AND current_status = 'Active'";
$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $todayRefills = $row['total'];
}

// Fetch Pending Refills
$pendingRefills = 0;
$sql = "SELECT COUNT(*) as total FROM sales WHERE DATE(next_refill_date) < CURDATE() AND status = 'Active' AND current_status = 'Active'";
$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $pendingRefills = $row['total'];
}

// Fetch Upcoming Refills (next 5 days)
$upcomingRefills = 0;
$sql = "SELECT COUNT(*) as total FROM sales WHERE next_refill_date > CURDATE() AND next_refill_date <= CURDATE() + INTERVAL 5 DAY AND status = 'Active' AND current_status = 'Active'";
$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $upcomingRefills = $row['total'];
}

// Fetch Total Refill Amount
$totalRefillAmount = 0;
$sql = "SELECT SUM(amount) as total FROM refill_history WHERE status = 'Active'";
$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $totalRefillAmount = $row['total'];
}

// Fetch This Month Sales
$monthSales = 0;
$sql = "SELECT COUNT(*) as total FROM sales WHERE MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE()) AND status = 'Active'";
$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $monthSales = $row['total'];
}

// Fetch This Month Income
$monthIncome = 0;
$sql = "SELECT SUM(rate) as total FROM sales WHERE MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE()) AND status = 'Active'";
$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $monthIncome = $row['total'];
}

// Fetch Total Sales
$totalSales = 0;
$sql = "SELECT COUNT(*) as total FROM sales WHERE status = 'Active'";
$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $totalSales = $row['total'];
}

// Fetch Total Income
$totalIncome = 0;
$sql = "SELECT SUM(rate) as total FROM sales WHERE status = 'Active'";
$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $totalIncome = $row['total'];
}

// Fetch Total Categories
$totalCategory = 0;
$sql = "SELECT COUNT(*) as total FROM category WHERE status = 'Active'";
$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $totalCategory = $row['total'];
}

// Fetch Total Products
$totalProducts = 0;
$sql = "SELECT COUNT(*) as total FROM product WHERE status = 'Active'";
$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $totalProducts = $row['total'];
}

// Return JSON
echo json_encode([
    'todayRefills' => $todayRefills,
    'pendingRefills' => $pendingRefills,
    'upcomingRefills' => $upcomingRefills,
    'totalRefillAmount' => $totalRefillAmount,
    'monthSales' => $monthSales,
    'monthIncome' => $monthIncome,
    'totalSales' => $totalSales,
    'totalIncome' => $totalIncome,
    'totalCategory' => $totalCategory,
    'totalProducts' => $totalProducts
]);
?>
