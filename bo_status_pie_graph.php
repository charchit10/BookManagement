<?php

include('connection.php');
$statuses = ['pending', 'complete', 'incomplete'];

$results = [];

// Loop through statuses and query
foreach ($statuses as $status) {
    $stmt = $conn->prepare("SELECT COUNT(*) as status_count FROM order_book WHERE order_book.status='" . $status . "'");
    $stmt->execute();
    $row = $stmt->fetch();

    $count = $row['status_count'];

    $results[] = [
        'name' => strtoupper($status),
        'y' => (int) $count
    ];
}
