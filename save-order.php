<?php
session_start();

$post_data = $_POST;
$books = $post_data['books'];
$qty = array_values($post_data['quantity']);

$post_data_arr = [];

foreach ($books as $key => $bid) {
    if (isset($qty[$key])) $post_data_arr[$bid] = $qty[$key];
}

// include connection
include('connection.php');

// Store data.
$batch = time();

$success = false;

try {
    foreach ($post_data_arr as $bid => $supplier_qty) {
        foreach ($supplier_qty as $sid => $qty) {
            // Insert to database.

            $values = [
                'supplier' => $sid,
                'book' => $bid,
                'quantity_ordered' => $qty,
                'status' => 'pending',
                'batch' => $batch,
                'created_by' => $_SESSION['user']['id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $sql = "INSERT INTO order_book
                        (supplier, book, quantity_ordered, status, batch, created_by, updated_at, created_at) 
                    VALUES 
                        (:supplier, :book, :quantity_ordered, :status, :batch, :created_by, :updated_at, :created_at)";
            $stmt = $conn->prepare($sql);
            $stmt->execute($values);
        }
    }
    $success = true;
    $message = 'Order successfully created!';
} catch (\Exception $e) {
    $message = $e->getMessage();
}

$_SESSION['response'] = [
    'message' => $message,
    'success' => $success
];

header('location:../book-order.php');
