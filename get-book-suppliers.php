<?php
include('connection.php');

$id = $_GET['id'];


//Fetch suppliers.
$stmt = $conn->prepare("
            SELECT supplier_name, suppliers.id
                FROM suppliers, booksuppliers 
                WHERE 
                    booksuppliers.book=$id
                        AND 
                    booksuppliers.supplier = suppliers.id
            ");
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($suppliers);
