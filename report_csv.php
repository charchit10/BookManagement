<?php
$type = $_GET['report'];
$file_name = '.xls';

$mapping_filenames = [
    'supplier' => 'Supplier Report',
    'book' => 'Book Report',
    'purchase_orders' => 'Book Order',
    'delivery' => 'Delivery Report'
];

$file_name = $mapping_filenames[$type] . '.xls';
header("Content-Disposition: attachment; filename=\"$file_name\"");
header("Content-Type: application/vnd.ms-excel");

// Pull data from database.
include('connection.php');

// Book Export
if ($type === 'book') {
    $stmt = $conn->prepare("SELECT *, books.id as bid FROM books
                            INNER JOIN users ON books.created_by = users.id
                            ORDER BY books.created_at DESC");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $books = $stmt->fetchAll();
    $is_header = true;
    foreach ($books as $book) {
        $book['created_by'] = $book['first_name'] . ' ' . $book['last_name'];
        unset($book['first_name'], $book['last_name'], $book['password'], $book['email']);

        if ($is_header) {
            $row = array_keys($book);
            $is_header = false;
            echo implode("\t", $row) . "\n";
        }

        // detect double-quotes and escape any value that contains them
        array_walk($book, function (&$str) {
            $str = preg_replace("/\t/", "\\t", $str);
            $str = preg_replace("/\r?\n/", "\\n", $str);
            if (strstr($str, '"')) $str = '"' . str_replace('"', '"', $str) . '"';
        });

        echo implode("\t", $book) . "\n";
    }
}

// Supplier Export
if ($type === 'supplier') {
    $stmt = $conn->prepare("SELECT suppliers.id as sid, suppliers.created_at as 'created at', users.first_name, users.last_name, suppliers.supplier_location, suppliers.email, suppliers.created_by FROM suppliers INNER JOIN users on suppliers.created_by = users.id ORDER BY suppliers.created_at DESC");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $suppliers = $stmt->fetchAll();

    $is_header = true;
    foreach ($suppliers as $supplier) {
        $supplier['created_by'] = $supplier['first_name'] . ' ' . $supplier['last_name'];
        unset($supplier['first_name'], $supplier['last_name']);

        if ($is_header) {
            $row = array_keys($supplier);
            $is_header = false;
            echo implode("\t", $row) . "\n";
        }

        // detect double-quotes and escape any value that contains them
        array_walk($supplier, function (&$str) {
            $str = preg_replace("/\t/", "\\t", $str);
            $str = preg_replace("/\r?\n/", "\\n", $str);
            if (strstr($str, '"')) $str = '"' . str_replace('"', '"', $str) . '"';
        });

        echo implode("\t", $supplier) . "\n";
    }
}

//Purchase Order Export
if ($type === 'purchase_orders') {
    $stmt = $conn->prepare("SELECT order_book.id, order_book.quantity_ordered, order_book.quantity_received, order_book.quantity_remaining, order_book.status, order_book.batch, users.first_name, users.last_name, suppliers.supplier_name, order_book.created_at as 'order book created at' FROM order_book
    INNER JOIN users ON order_book.created_by = users.id
    INNER JOIN suppliers ON order_book.supplier = suppliers.id
    ORDER BY order_book.batch DESC
    ");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $order_books = $stmt->fetchAll();

    //Group by batch
    $bos = [];
    foreach ($order_books as $order_book) {
        $bos[$order_book['batch']][] = $order_book;
    }

    $is_header = true;

    foreach ($bos as $order_books) {
        foreach ($order_books as $order_book) {
            $order_book['created_by'] = $order_book['first_name'] . ' ' . $order_book['last_name'];
            unset($order_book['first_name'], $order_book['last_name']);

            if ($is_header) {
                $row = array_keys($order_book);
                $is_header = false;
                echo implode("\t", $row) . "\n";
            }

            // detect double-quotes and escape any value that contains them
            array_walk($order_book, function (&$str) {
                $str = preg_replace("/\t/", "\\t", $str);
                $str = preg_replace("/\r?\n/", "\\n", $str);
                if (strstr($str, '"')) $str = '"' . str_replace('"', '"', $str) . '"';
            });

            echo implode("\t", $order_book) . "\n";
        }

        // New line
        echo "\n";
    }
}


// Delivery Export
if ($type === 'delivery') {
    $stmt = $conn->prepare("SELECT date_received, qty_received, first_name, last_name, books.book_name, supplier_name, batch
    FROM order_book_history, order_book, users, suppliers, books
    WHERE
        order_book_history.order_book_id = order_book.id
    AND
        order_book.created_by = users.id
    AND
        order_book.supplier = suppliers.id
        AND
        order_book.book = books.id
    ORDER BY order_book.batch DESC
    ");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $deliveries = $stmt->fetchAll();

    //Group by batch
    $delivery_by_batch = [];
    foreach ($deliveries as $delivery) {
        $delivery_by_batch[$delivery['batch']][] = $delivery;
    }

    $is_header = true;

    foreach ($delivery_by_batch as $deliveries) {
        foreach ($deliveries as $delivery) {
            $delivery['created_by'] = $delivery['first_name'] . ' ' . $delivery['last_name'];
            unset($delivery['first_name'], $delivery['last_name']);

            if ($is_header) {
                $row = array_keys($delivery);
                $is_header = false;
                echo implode("\t", $row) . "\n";
            }

            // detect double-quotes and escape any value that contains them
            array_walk($delivery, function (&$str) {
                $str = preg_replace("/\t/", "\\t", $str);
                $str = preg_replace("/\r?\n/", "\\n", $str);
                if (strstr($str, '"')) $str = '"' . str_replace('"', '"', $str) . '"';
            });

            echo implode("\t", $delivery) . "\n";
        }

        // New line
        echo "\n";
    }
}
