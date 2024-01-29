<?php
require('fpdf/fpdf.php');
class PDF extends FPDF
{
    function __construct()
    {
        parent::__construct('L');
    }


    // Colored table
    function FancyTable($headers, $data, $row_height = 30)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');

        $width_sum = 0;
        foreach ($headers as $header_key => $header_data) {
            $this->Cell($header_data['width'], 7, $header_key, 1, 0, 'C', true);
            $width_sum += $header_data['width'];
        }
        $this->Ln();
        // Color and font restoration
        $this->SetTextColor(0);
        $this->SetFont('');

        $img_pos_y = 40;
        $header_keys = array_keys($headers);
        foreach ($data as $row) {
            foreach ($header_keys as $header_key) {
                $content = $row[$header_key]['content'];
                $width = $headers[$header_key]['width'];
                $align = $row[$header_key]['align'];

                if ($header_key == 'image')
                    $content = is_null($content) || $content == "" ? 'No Image' : $this->Image('.././database/uploads/books/' . $content, 45, $img_pos_y, 30, 25);

                $this->Cell($width, $row_height, $content, 'LRBT', 0, $align);
            }

            $this->Ln();
            $img_pos_y += 30;
        }


        // Closing line
        $this->Cell($width_sum, 0, '', 'T');
    }
}

$type = $_GET['report'];
$report_headers = [
    'book' => 'Book Reports',
    'supplier' => 'Supplier Report',
    'delivery' => 'Delivery Report',
    'purchase_orders' => 'Purchase Order Report'
];
$row_height;

// Pull data from database.
include('connection.php');

// Book Export
if ($type == 'book') {
    // Column headings  - replace from mysql database ot hardcore it
    $headers = [
        'id' => [
            'width' => 15
        ],
        'image' => [
            'width' => 70
        ],
        'book_name' => [
            'width' => 35
        ],
        'stock' => [
            'width' => 15
        ],
        'created_by' => [
            'width' => 45
        ],
        'created_at' => [
            'width' => 45
        ],
        'updated_at' => [
            'width' => 45
        ]
    ];

    // Load Book
    $stmt = $conn->prepare("SELECT *, books.id as bid FROM books 
            INNER JOIN
                users on 
                books.created_by = users.id 
                ORDER BY 
                books.created_at DESC");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $books = $stmt->fetchAll();


    $data = [];
    foreach ($books as $book) {

        $book['created_by'] = $book['first_name'] . ' ' . $book['last_name'];
        unset($book['first_name'], $book['last_name'], $book['password'], $book['email']);

        // detect double-quotes and escape any value that contains them
        array_walk($book, function (&$str) {
            $str = preg_replace("/\t/", "\\t", $str);
            $str = preg_replace("/\r?\n/", "\\n", $str);
            if (strstr($str, '"')) $str = '"' . str_replace('"', '"', $str) . '"';
        });

        $data[] = [
            'id' => [
                'content' => $book['bid'],
                'align' => 'C'
            ],
            'image' => [
                'content' => $book['img'],
                'align' => 'C'
            ],
            'book_name' => [
                'content' => $book['book_name'],
                'align' => 'C'
            ],
            'stock' => [
                'content' => number_format($book['stock']),
                'align' => 'C'
            ],
            'created_by' => [
                'content' => $book['created_by'],
                'align' => 'L'
            ],
            'created_at' => [
                'content' => date('M d,Y h:i:s A', strtotime($book['created_at'])),
                'align' => 'L'
            ],
            'updated_at' => [
                'content' => date('M d,Y h:i:s A', strtotime($book['updated_at'])),
                'align' => 'L'
            ],
        ];
    }
    $row_height = 30;
}

// Supplier Export
if ($type === 'supplier') {
    $stmt = $conn->prepare("SELECT suppliers.id as sid, suppliers.created_at as 'created at', users.first_name, users.last_name, suppliers.supplier_location, suppliers.email, suppliers.created_by FROM suppliers INNER JOIN users on suppliers.created_by = users.id ORDER BY suppliers.created_at DESC");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $suppliers = $stmt->fetchAll();

    //// Column headings  - replace from mysql database ot hardcore it
    $headers = [
        'supplier_id' => [
            'width' => 15
        ],
        'created at' => [
            'width' => 70
        ],
        'supplier_location' => [
            'width' => 60
        ],
        'email' => [
            'width' => 60
        ],
        'created_by' => [
            'width' => 60
        ]
    ];

    foreach ($suppliers as $supplier) {
        $supplier['created_by'] = $supplier['first_name'] . ' ' . $supplier['last_name'];

        $data[] = [
            'supplier_id' => [
                'content' => $supplier['sid'],
                'align' => 'C'
            ],
            'created at' => [
                'content' => $supplier['created at'],
                'align' => 'C'
            ],
            'supplier_location' => [
                'content' => $supplier['supplier_location'],
                'align' => 'C'
            ],
            'email' => [
                'content' => $supplier['email'],
                'align' => 'C'
            ],
            'created_by' => [
                'content' => $supplier['created_by'],
                'align' => 'C'
            ]
        ];
    }

    $row_height = 10;
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

    //// Column headings  - replace from mysql database ot hardcore it
    $headers = [
        'date_received' => [
            'width' => 40
        ],
        'qty_received' => [
            'width' => 30
        ],
        'book_name' => [
            'width' => 50
        ],
        'supplier_name' => [
            'width' => 60
        ],
        'batch' => [
            'width' => 35
        ],
        'created_by' => [
            'width' => 60
        ]
    ];

    $deliveries = $stmt->fetchAll();

    foreach ($deliveries as $delivery) {
        $delivery['created_by'] = $delivery['first_name'] . ' ' . $delivery['last_name'];

        $data[] = [
            'date_received' => [
                'content' => $delivery['date_received'],
                'align' => 'C'
            ],
            'qty_received' => [
                'content' => $delivery['qty_received'],
                'align' => 'C'
            ],
            'book_name' => [
                'content' => $delivery['book_name'],
                'align' => 'C'
            ],
            'supplier_name' => [
                'content' => $delivery['supplier_name'],
                'align' => 'C'
            ],
            'batch' => [
                'content' => $delivery['batch'],
                'align' => 'C'
            ],
            'created_by' => [
                'content' => $delivery['created_by'],
                'align' => 'C'
            ]
        ];
    }
    $row_height = 10;
}

//Purchase Order Export
if ($type === 'purchase_orders') {
    $stmt = $conn->prepare("SELECT books.book_name, order_book.id, order_book.quantity_ordered, order_book.quantity_received, order_book.quantity_remaining, order_book.status, order_book.batch, users.first_name, users.last_name, suppliers.supplier_name, order_book.created_at as 'created at' 
    FROM order_book
    INNER JOIN users ON order_book.created_by = users.id
    INNER JOIN suppliers ON order_book.supplier = suppliers.id
    INNER JOIN books ON order_book.book = books.id
    ORDER BY order_book.batch DESC
    ");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $headers = [
        'id' => [
            'width' => 10
        ],
        'qty_ordered' => [
            'width' => 23
        ],
        'qty_received' => [
            'width' => 23
        ],
        'qty_remaining' => [
            'width' => 25
        ],
        'status' => [
            'width' => 25
        ],
        'batch' => [
            'width' => 25
        ],
        'supplier_name' => [
            'width' => 50
        ],
        'book_name' => [
            'width' => 35
        ],
        'created at' => [
            'width' => 40
        ],
        'created_by' => [
            'width' => 30
        ]
    ];


    $order_books = $stmt->fetchAll();

    foreach ($order_books as $order_book) {
        $order_book['created_by'] = $order_book['first_name'] . ' ' . $order_book['last_name'];

        $data[] = [
            'id' => [
                'content' => $order_book['id'],
                'align' => 'C'
            ],
            'qty_ordered' => [
                'content' => $order_book['quantity_ordered'],
                'align' => 'C'
            ],
            'qty_received' => [
                'content' => $order_book['quantity_received'],
                'align' => 'C'
            ],
            'qty_remaining' => [
                'content' => $order_book['quantity_remaining'],
                'align' => 'C'
            ],
            'status' => [
                'content' => $order_book['status'],
                'align' => 'C'
            ],
            'batch' => [
                'content' => $order_book['batch'],
                'align' => 'C'
            ],
            'supplier_name' => [
                'content' => $order_book['supplier_name'],
                'align' => 'C'
            ],
            'book_name' => [
                'content' => $order_book['book_name'],
                'align' => 'C'
            ],
            'created at' => [
                'content' => $order_book['created at'],
                'align' => 'C'
            ],
            'created_by' => [
                'content' => $order_book['created_by'],
                'align' => 'C'
            ],
        ];
    }

    $row_height = 10;
}

// Start PDF
$pdf = new PDF();
$pdf->SetFont('Arial', '', 20);
$pdf->AddPage();

$pdf->Cell(80);
$pdf->Cell(100, 10, $report_headers[$type], 0, 0, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Ln();
$pdf->Ln();

$pdf->FancyTable($headers, $data, $row_height);
$pdf->Output();
