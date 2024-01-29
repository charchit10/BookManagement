<?php
include('connection.php');

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM order_book_history WHERE order_book_id=$id ORDER BY date_received DESC");
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);

echo json_encode($stmt->fetchAll());
