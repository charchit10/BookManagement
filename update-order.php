<?php
$purchase_orders = $_POST['payload'];

include('connection.php');
try {

    foreach ($purchase_orders as $bo) {
        $delivered = (int) $bo['qtyDelivered'];

        // We don't save the data if it's zero received
        if ($delivered > 0) {
            $cur_qty_received = (int) $bo['qtyReceive'];
            $status = $bo['status'];
            $row_id = $bo['id'];
            $qty_ordered = (int) $bo['qtyOrdered'];
            $book_id = (int) $bo['bid'];


            // Update quantity received
            $updated_qty_received = $cur_qty_received + $delivered;
            $qty_remaining = $qty_ordered - $updated_qty_received;


            $sql = "Update order_book 
                        SET 
                        quantity_received=?, status=?, quantity_remaining=?
                        WHERE id=?";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$updated_qty_received, $status, $qty_remaining, $row_id]);


            // Insert script adding to the order_book_history
            $delivery_history = [
                'order_book_id' => $row_id,
                'qty_received' => $delivered,
                'date_received' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s')
            ];

            $sql = "INSERT INTO order_book_history
                        (order_book_id, qty_received, date_received, date_updated)
                    VALUES 
                        (:order_book_id, :qty_received, :date_received, :date_updated)";
            $stmt = $conn->prepare($sql);
            $stmt->execute($delivery_history);


            // script for updating the main book quantity
            // Select statement - to pull the current quantity of book,

            $stmt = $conn->prepare("
                                    SELECT books.stock FROM books
                                        WHERE
                                            id = $book_id
                                            ");
            $stmt->execute();
            $book = $stmt->fetch();

            $cur_stock = (int) $book['stock'];

            // Update statement - to add the delivered book to the cur quantity
            $updated_stock = $cur_stock + $delivered;
            $sql = "UPDATE books
                            SET 
                                stock=?
                            WHERE id=?";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$updated_stock, $book_id]);
        }
    }

    $response = [
        'success' => true,
        'message' => "Purchase order successfully updated!."
    ];
} catch (\Exception $e) {
    $response = [
        'success' => false,
        'message' => "Error processing your request."
    ];
}

echo json_encode($response);
