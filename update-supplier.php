<?php
$supplier_name = isset($_POST['supplier_name']) ? $_POST['supplier_name'] : '';
$supplier_location = isset($_POST['supplier_location']) ? $_POST['supplier_location'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';

$supplier_id = $_POST['sid'];


//Update the book record
try {
    $sql = "Update suppliers 
SET 
supplier_name = ?, supplier_location = ?, email = ?
WHERE id=?";
    include('connection.php');
    $stmt = $conn->prepare($sql);
    $stmt->execute([$supplier_name, $supplier_location, $email, $supplier_id]);

    //Delete the old values.
    $sql = "DELETE FROM booksuppliers WHERE supplier=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$supplier_id]);

    // Loop through the supplier and add record
    // Get suppliers.
    $books = isset($_POST['books']) ? $_POST['books'] : [];
    foreach ($books as $book) {
        $supplier_data = [
            'supplier_id' => $supplier_id,
            'book_id' => $book,
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        // Further processing for each $supplier_data goes here



        $sql = "INSERT INTO booksuppliers(supplier, book, updated_at, created_at) VALUES (:supplier_id, :book_id, :updated_at, :created_at)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($supplier_data);
    }

    $response = [
        'success' => true,
        'message' => "<strong>$supplier_name</strong> Successfully updated to the system."
    ];
} catch (\Exception $e) {
    $response = [
        'success' => false,
        'message' => "Error processing your request."
    ];
}

echo json_encode($response); // Corrected function name
