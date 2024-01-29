<?php
include('connection.php');

$id = $_GET['id'];


$stmt = $conn->prepare("SELECT * FROM suppliers WHERE id=$id");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);


//Fetch books.
$stmt = $conn->prepare("
            SELECT book_name, books.id
                FROM books, booksuppliers 
                WHERE 
                    booksuppliers.supplier=$id
                        AND 
                    booksuppliers.book = books.id
            ");
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$row['books'] = array_column($books, 'id');

echo json_encode($row);
