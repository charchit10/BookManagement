<?php
//Start the session.
session_start();
if (!isset($_SESSION['user'])) header('location: login.php');

// Get all books.
$show_table = 'books';
$books = include('database/show.php');

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books - Book Store Management System</title>
    <?php include('partials/app-header-scripts.php'); ?>
</head>

<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php') ?>
        <div class="dashboard_content_container" id="dashboard_content_container">
            <?php include('partials/app-topnav.php') ?>
            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <div class="row">
                        <div class="column column-12">
                            <h1 class="section_header"> <i class="fa fa-list"></i> List of Books</h1>
                            <div class="section_content">
                                <div class="users">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Image</th>
                                                <th>Book Name</th>
                                                <th>Stock</th>
                                                <th width="20%">Description</th>
                                                <th width="15%">Suppliers</th>
                                                <th>Created BY</th>
                                                <th>Created At</th>
                                                <th>Updated At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($books as $index => $book) { ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td class="firstName">
                                                        <img class="bookImages" src="database/uploads/books/<?= $book['img'] ?>" alt="" />
                                                    </td>
                                                    <td class="lastName"><?= $book['book_name'] ?></td>
                                                    <td class="lastName"><?= number_format($book['stock']) ?></td>
                                                    <td class="description"><?= $book['description'] ?></td>
                                                    <td class="email">
                                                        <?php
                                                        $supplier_list = '-';


                                                        $bid = $book['id'];
                                                        $stmt = $conn->prepare("
                                                            SELECT supplier_name
                                                            FROM suppliers, booksuppliers 
                                                            WHERE 
                                                                booksuppliers.book=$bid
                                                                    AND 
                                                                booksuppliers.supplier = suppliers.id
                                                        ");
                                                        $stmt->execute();
                                                        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                        if ($row) {
                                                            $supplier_arr = array_column($row, 'supplier_name');
                                                            $supplier_list = '<li>' . implode("</li><li>", $supplier_arr);
                                                        }

                                                        echo $supplier_list;
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $uid = $book['created_by'];
                                                        $stmt = $conn->prepare("SELECT * FROM users WHERE id=$uid");
                                                        $stmt->execute();
                                                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                                                        $created_by_name = $row['first_name'] . ' ' . $row['last_name'];
                                                        echo $created_by_name;
                                                        ?>
                                                    </td>
                                                    <td><?= date('M d, Y @ h:i:s A ', strtotime($book['created_at'])) ?></td>
                                                    <td><?= date('M d, Y @ h:i:s A ', strtotime($book['updated_at'])) ?></td>
                                                    <td>
                                                        <a href="" class="updateBook" data-bid="<?= $book['id'] ?>"><i class="fa fa-pencil"></i> Edit</a> |
                                                        <a href="" class="deleteBook" data-name="<?= $book['book_name'] ?>" data-bid="<?= $book['id'] ?>">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </a>

                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <p class="userCount"><?= count($books) ?> books </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include('partials/app-scripts.php');

    $show_table = 'suppliers';
    $suppliers = include('database/show.php');

    $suppliers_arr = [];

    foreach ($suppliers as $supplier) {
        $suppliers_arr[$supplier['id']] = $supplier['supplier_name'];
    }

    $suppliers_arr = json_encode($suppliers_arr);
    ?>
    <script>
        var suppliersList = <?= $suppliers_arr ?>;

        function script() {
            var vm = this;

            this.registerEvents = function() {
                    document.addEventListener('click', function(e) {
                        var targetElement = e.target;
                        var classList = targetElement.classList;

                        if (classList.contains('deleteBook')) {
                            e.preventDefault();

                            var bId = targetElement.dataset.bid;
                            var bName = targetElement.dataset.name;

                            BootstrapDialog.confirm({
                                type: BootstrapDialog.TYPE_DANGER,
                                title: 'Delete Book',
                                message: 'Are you sure to delete <strong>' + bName + '</strong>?',
                                callback: function(isDelete) {
                                    if (isDelete) {
                                        $.ajax({
                                            type: 'POST',
                                            dataType: 'json',
                                            data: {
                                                id: bId,
                                                table: 'books'
                                            },
                                            url: 'database/delete.php',
                                            success: function(data) {
                                                var message = data.success ?
                                                    bName + ' successfully deleted!' : 'Error processing your request!';

                                                BootstrapDialog.alert({
                                                    type: data.success ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER,
                                                    message: message,
                                                    callback: function() {
                                                        if (data.success) location.reload();
                                                    }
                                                });

                                            }
                                        });
                                    }
                                }
                            });
                        } else if (classList.contains('updateBook')) {
                            e.preventDefault();
                            var bId = targetElement.dataset.bid;
                            vm.showEditDialog(bId);
                        }
                    });


                    document.addEventListener('submit', function(e) {
                        e.preventDefault();
                        var targetElement = e.target; // target element

                        if (targetElement.id === 'editSupplierForm') {
                            vm.saveUpdatedData(targetElement);
                        }

                    })
                },

                this.saveUpdatedData = function(form) {
                    $.ajax({
                        method: 'POST',
                        dataType: 'json',
                        data: new FormData(form),
                        processData: false,
                        contentType: false,
                        url: 'database/update-book.php',
                        success: function(data) {
                            BootstrapDialog.alert({
                                type: data.success ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER,
                                message: data.message,
                                callback: function() {
                                    if (data.success) location.reload();

                                }
                            });
                        }
                    });
                },

                this.showEditDialog = function(id) {
                    $.get('database/get-book.php', {
                        id: id
                    }, function(bookDetails) {
                        let curSuppliers = bookDetails['suppliers'];
                        let supplierOption = '';


                        for (const [supId, supName] of Object.entries(suppliersList)) {
                            selected = curSuppliers.indexOf(supId) > -1 ? 'selected' : '';
                            supplierOption += "<option " + selected + " value='" + supId + "'>" + supName + "</option>";
                        }



                        BootstrapDialog.confirm({
                            title: 'Update <strong>' + bookDetails.book_name + '</strong>',
                            message: '<form action="database/add.php" method="POST" enctype="multipart/form-data" id="editSupplierForm">\
                            <div class="appFormInputContainer">\
                                <label for="book_name">Book Name</label>\
                                <input type="text" class="appFormInput" id="book_name" value="' + bookDetails.book_name + '" placeholder="Enter book name..." name="book_name" />\
                            </div>\
                            <div class="appFormInputContainer">\
                                <label for="description">Suppliers</label>\
                                <select name="suppliers[]" id="suppliersSelect" multiple="">\
                                    <option value=""> Select Supplier</option>\
                                    ' + supplierOption + '\
                                </select>\
                            </div>\
                            <div class="appFormInputContainer">\
                                <label for="description">Description</label>\
                                <textarea class="appFormInput bookTextAreaInput" placeholder="Enter book description..." id="description" name="description"> ' + bookDetails.description + '</textarea> \
                            </div>\
                            <div class="appFormInputContainer">\
                                <label for="book_name">Book Image</label>\
                                <input type="file" name="img" />\
                            </div>\
                            <input type="hidden" name="bid" value="' + bookDetails.id + '" />\
                            <input type="submit" value="submit" id="editBookSubmitBtn" class="hidden"/>\
                            </form>\
                            ',
                            callback: function(isUpdate) {
                                if (isUpdate) { // if user click 'ok' button.
                                    document.getElementById('editBookSubmitBtn').click();
                                }

                            }
                        });
                    }, 'json');
                },
                this.initialize = function() {
                    this.registerEvents();
                }
        }
        var scriptInstance = new script();
        scriptInstance.initialize();
    </script>

</body>

</html>