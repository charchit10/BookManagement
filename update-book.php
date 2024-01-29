<?php
$book_name = $_POST['book_name'];
$description = $_POST['description'];
$bid = $_POST['bid'];


//Upload or move the file to our directory
$target_dir = "../database/uploads/books/";

$file_name_value = null; // Initialize it as null
$file_data = $_FILES['img'];

if ($file_data['tmp_name'] !== '') {
    $file_name = $file_data['name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $file_name = 'book-' . time() . '.' . $file_ext;


    $check = getimagesize($file_data['tmp_name']);
    // Move the file
    if ($check) {
        if (move_uploaded_file($file_data['tmp_name'], $target_dir . $file_name)) {
            // Save the file_name to the database.
            $file_name_value = $file_name;
        }
    }
}



//Update the book record
try {
    $sql = "Update books 
SET 
book_name=?, description=?, img=?
WHERE id=?";
    include('connection.php');
    $stmt = $conn->prepare($sql);
    $stmt->execute([$book_name, $description, $file_name_value, $bid]);

    //Delete the old values.
    $sql = "DELETE FROM booksuppliers WHERE book=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$bid]);

    // Get suppliers.
    $suppliers = isset($_POST['suppliers']) ? $_POST['suppliers'] : [];
    foreach ($suppliers as $supplier) {
        $supplier_data = [
            'supplier_id' => $supplier,
            'book_id' => $bid,
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $sql = "INSERT INTO booksuppliers(supplier, book, updated_at, created_at) VALUES (:supplier_id, :book_id, :updated_at, :created_at)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($supplier_data);
    }
    $response = [
        'success' => true,
        'message' => "<strong>$book_name</strong> Successfully updated to the system."
    ];
} catch (\Exception $e) {
    $response = [
        'success' => false,
        'message' => "Error processing your request."
    ];
}

echo json_encode($response); // Corrected function name
