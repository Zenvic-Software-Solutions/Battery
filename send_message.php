<?php
require 'vendor/autoload.php';
include 'db/dbConnection.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$auth = [
    'VAPID' => [
        'subject' => 'mailto:zenvicsoft@gmail.com',
        'publicKey' => 'BPz7YYRAIRWV07EeF68n-vaivYU9Y14O5W67IhcY24dBypgpA7kGqeMC_NOwnouJwWQLSAV_Jy-qR2776c78MYM',
        'privateKey' => '9CrKzduTNhW-8Y9HW-Sf2DEcZl7DYX8owEQJnYne478',
    ],
];

$today = date('Y-m-d');

$sql = "SELECT COUNT(*) as due_count FROM sales s LEFT JOIN product p ON p.id = s.product_id WHERE DATE(s.next_refill_date) = '$today' AND s.status = 'Active' AND s.current_status = 'Active' AND p.reminder_status = 'Active'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$dueCount = (int)$row['due_count'];

$adminSql = "SELECT * FROM push_subscriptions WHERE user_id = 1 AND status = 'Active'";
$adminResult = mysqli_query($conn, $adminSql);

if (!$adminResult || mysqli_num_rows($adminResult) === 0) {
    exit("❌ No admin push subscription found.");
}

$webPush = new WebPush($auth);

$title = "Battery Refill Check";
$body = $dueCount > 0
    ? "⏰ $dueCount battery refill(s) scheduled for today. Check the list."
    : "✅ No battery refills due today.";

while ($row = mysqli_fetch_assoc($adminResult)) {
    $subscription = Subscription::create([
        'endpoint' => $row['endpoint'],
        'keys' => [
            'p256dh' => $row['public_key'],
            'auth' => $row['auth_token'],
        ],
    ]);

    $payload = json_encode([
        'title' => $title,
        'body' => $body,
        'url'   => 'https://battery.zenvicsoft.com/reminders.php',
    ]);

    $webPush->queueNotification($subscription, $payload);
}

foreach ($webPush->flush() as $report) {
    $endpoint = $report->getRequest()->getUri()->__toString();

    if ($report->isSuccess()) {
        echo "Notification sent to {$endpoint}<br>";
    } else {
        echo "Failed to send to {$endpoint}: {$report->getReason()}<br>";
    }
}
?>