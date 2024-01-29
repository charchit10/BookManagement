<?php
//Start the session.
session_start();
if (!isset($_SESSION['user'])) header('location: login.php');


$show_table = 'suppliers';
$suppliers = include('database/show.php');

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Suppliers - Book Store Management System</title>
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
                            <h1 class="section_header"> <i class="fa fa-list"></i> List of Suppliers</h1>
                            <div class="section_content">
                                <div class="users">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Supplier Name</th>
                                                <th>Supplier Location</th>
                                                <th>Contact Details</th>
                                                <th>Books</th>
                                                <th>Created BY</th>
                                                <th>Created At</th>
                                                <th>Updated At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($suppliers as $index => $supplier) { ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td>
                                                        <?= $supplier['supplier_name'] ?>
                                                    </td>
                                                    <td><?= $supplier['supplier_location'] ?></td>
                                                    <td><?= $supplier['email'] ?></td>
                                                    <td>
                                                        <?php
                                                        $book_list = '-';


                                                        $sid = $supplier['id'];
                                                        $stmt = $conn->prepare("
                                                            SELECT book_name
                                                            FROM books, booksuppliers 
                                                            WHERE 
                                                                booksuppliers.supplier=$sid
                                                                    AND 
                                                                booksuppliers.book = books.id
                                                        ");
                                                        $stmt->execute();
                                                        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                        if ($row) {
                                                            $book_arr = array_column($row, 'book_name');
                                                            $book_list = '<li>' . implode("</li><li>", $book_arr);
                                                        }

                                                        echo $book_list;
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $uid = $supplier['created_by'];
                                                        $stmt = $conn->prepare("SELECT * FROM users WHERE id=$uid");
                                                        $stmt->execute();
                                                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                                                        $created_by_name = $row['first_name'] . ' ' . $row['last_name'];
                                                        echo $created_by_name;
                                                        ?>
                                                    </td>
                                                    <td><?= date('M d, Y @ h:i:s A ', strtotime($supplier['created_at'])) ?></td>
                                                    <td><?= date('M d, Y @ h:i:s A ', strtotime($supplier['updated_at'])) ?></td>
                                                    <td>
                                                        <a href="" class="updateSupplier" data-sid="<?= $supplier['id'] ?>"><i class="fa fa-pencil"></i> Edit</a> |
                                                        <a href="" class="deleteSupplier" data-name="<?= $supplier['supplier_name'] ?>" data-sid="<?= $supplier['id'] ?>">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <p class="userCount"><?= count($suppliers) ?> suppliers </p>
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

    $show_table = 'books';
    $books = include('database/show.php');

    $books_arr = [];

    foreach ($books as $book) {
        $books_arr[$book['id']] = $book['book_name'];
    }

    $books_arr = json_encode($books_arr);
    ?>
    <script>
        var booksList = <?= $books_arr ?>;

        function script() {
            var vm = this;

            this.registerEvents = function() {
                    document.addEventListener('click', function(e) {
                        var targetElement = e.target;
                        var classList = targetElement.classList;

                        if (classList.contains('deleteSupplier')) {
                            e.preventDefault();

                            var sId = targetElement.dataset.sid;
                            var supplierName = targetElement.dataset.name;

                            BootstrapDialog.confirm({
                                type: BootstrapDialog.TYPE_DANGER,
                                title: 'Delete Supplier',
                                message: 'Are you sure to delete <strong>' + supplierName + '</strong>?',
                                callback: function(isDelete) {
                                    if (isDelete) {
                                        $.ajax({
                                            type: 'POST',
                                            dataType: 'json',
                                            data: {
                                                id: sId,
                                                table: 'suppliers'
                                            },
                                            url: 'database/delete.php',
                                            success: function(data) {
                                                var message = data.success ?
                                                    supplierName + ' successfully deleted!' : 'Error processing your request!';

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
                        } else if (classList.contains('updateSupplier')) {
                            e.preventDefault();
                            var sId = targetElement.dataset.sid;
                            vm.showEditDialog(sId);
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
                        data: {
                            supplier_name: document.getElementById('supplier_name').value,
                            supplier_location: document.getElementById('supplier_location').value,
                            email: document.getElementById('email').value,
                            books: $('#books').val(),
                            sid: document.getElementById('sid').value
                        },
                        url: 'database/update-supplier.php',
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
                    $.get('database/get-supplier.php', {
                        id: id
                    }, function(supplierDetails) {
                        let curBooks = supplierDetails['books'];
                        let bookOptions = '';


                        for (const [bId, bName] of Object.entries(booksList)) {
                            selected = curBooks.indexOf(bId) > -1 ? 'selected' : '';
                            bookOptions += "<option " + selected + " value='" + bId + "'>" + bName + "</option>";
                        }



                        BootstrapDialog.confirm({
                            title: 'Update <strong>' + supplierDetails.supplier_name + '</strong>',
                            message: '<form action="database/add.php" method="POST" enctype="multipart/form-data" id="editSupplierForm">\
                            <div class="appFormInputContainer">\
                                        <label for="supplier_name">Supplier Name</label>\
                                        <input type="text" class="appFormInput" id="supplier_name" value="' + supplierDetails.supplier_name + '" placeholder="Enter supplier name..." name="supplier_name" />\
                                    </div>\
                                    <div class="appFormInputContainer">\
                                        <label for="supplier_location">Location</label>\
                                        <input type="text" class="appFormInput" value="' + supplierDetails.supplier_location + '" placeholder="Enter book supplier location..." id="supplier_location" name="supplier_location">\
                                    </div>\
                                    <div class="appFormInputContainer">\
                                        <label for="email">Email</label>\
                                        <input type="text" class="appFormInput" value="' + supplierDetails.email + '"placeholder="Enter supplier email..." id="email" name="email">\
                                    </div>\
                                    <div class="appFormInputContainer">\
                                        <label for="books">Books</label>\
                                        <select name="books[]" id="books" multiple="">\
                                        <option value="">Select Books</option>\
                                        ' + bookOptions + '\
                                        </select>\
                                    </div>\
                                        <input type="hidden" name="sid" id="sid" value="' + supplierDetails.id + '" />\
                                        <input type="submit" value="submit" id="editSupplierSubmitBtn" class="hidden"/>\
                                        </form>\
                                        ',
                            callback: function(isUpdate) {
                                if (isUpdate) { // if user click 'ok' button.
                                    document.getElementById('editSupplierSubmitBtn').click();
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