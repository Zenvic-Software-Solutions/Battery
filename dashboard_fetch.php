<?php
include "db/dbConnection.php";

// Function to run queries and get totals
function fetchValue($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['total'] ?? 0;
    }
    return 0;
}

// Function to get dashboard data
function getDashboardData($conn) {
    return [
        'todayRefills'     => fetchValue($conn, "SELECT COUNT(*) as total FROM sales WHERE DATE(next_refill_date) = CURDATE() AND status = 'Active' AND current_status = 'Active'"),
        'pendingRefills'   => fetchValue($conn, "SELECT COUNT(*) as total FROM sales WHERE DATE(next_refill_date) < CURDATE() AND status = 'Active' AND current_status = 'Active'"),
        'upcomingRefills'  => fetchValue($conn, "SELECT COUNT(*) as total FROM sales WHERE next_refill_date > CURDATE() AND next_refill_date <= CURDATE() + INTERVAL 5 DAY AND status = 'Active' AND current_status = 'Active'"),
        'totalRefillAmount'=> fetchValue($conn, "SELECT SUM(amount) as total FROM refill_history WHERE status = 'Active'"),
        'monthSales'       => fetchValue($conn, "SELECT COUNT(*) as total FROM sales WHERE MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE()) AND status = 'Active'"),
        'monthIncome'      => fetchValue($conn, "SELECT SUM(rate) as total FROM sales WHERE MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE()) AND status = 'Active'"),
        'totalSales'       => fetchValue($conn, "SELECT COUNT(*) as total FROM sales WHERE status = 'Active'"),
        'totalIncome'      => fetchValue($conn, "SELECT SUM(rate) as total FROM sales WHERE status = 'Active'"),
        'totalCategory'    => fetchValue($conn, "SELECT COUNT(*) as total FROM category WHERE status = 'Active'"),
        'totalProducts'    => fetchValue($conn, "SELECT COUNT(*) as total FROM product WHERE status = 'Active'")
    ];
}

