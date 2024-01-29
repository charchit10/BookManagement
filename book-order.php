<?php
//Start the session.
session_start();
if (!isset($_SESSION['user'])) header('location: login.php');

// Get all books.
$show_table = 'books';
$books = include('database/show.php');
$books = json_encode($books);

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Book - Book Store Management System</title>

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
                            <h1 class="section_header"> <i class="fa fa-plus"></i> Order Book</h1>
                            <div>
                                <form action="database/save-order.php" method="POST">
                                    <div class="alignRight">
                                        <button type="button" class="orderBtn orderBookBtn" id="orderBookBtn">Add Another Book</button>
                                    </div>

                                    <div id="orderBookLists">
                                        <P id="noData" style="color: #9f9f9f;"> No Books selected. </P>
                                    </div>

                                    <div class="alignRight marginTop20">
                                        <button type="submit" class="orderBtn submitOrderBookBtn">Submit Order</button>
                                    </div>
                                </form>
                            </div>
                            <?php
                            if (isset($_SESSION['response'])) {
                                $responseMessage = $_SESSION['response']['message'];
                                $is_success = $_SESSION['response']['success'];
                            ?>
                                <div class="responseMessage">
                                    <p class="responseMessage <?= $is_success ? 'responseMessage__success' : 'responseMessage__error' ?>">
                                        <?= $responseMessage ?>
                                    </p>
                                </div>
                            <?php
                                unset($_SESSION['response']);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('partials/app-scripts.php'); ?>

    <script>
        var books = <?= $books ?>;
        var counter = 0;

        function Script() {
            var vm = this;

            var self = this; // Store a reference to 'this'

            this.bookOptions = '\
            <div>\
                <label for="book_name">BOOK NAME</label>\
                <select name="books[]" class="bookNameSelect" id="book_name">\
                    <option value="">Select Book</option>\
                    INSERTBOOKTHERE\
                </select>\
                    <button class="appbtn removeOrderBtn">Remove</button>\
            </div>';


            this.initialize = function() {
                    this.registerEvents();
                    this.renderBookOptions();
                },

                this.renderBookOptions = function() {
                    let optionHtml = '';
                    books.forEach((book) => {
                        optionHtml += '<option value="' + book.id + '">' + book.book_name + '</option>';
                    })

                    selfbookOptions = self.bookOptions.replace('INSERTBOOKTHERE', optionHtml); // Use 'self' here
                },

                this.registerEvents = function() {
                    document.addEventListener('click', function(e) {
                        var targetElement = e.target; // Target element
                        var classList = targetElement.classList;

                        // Add new book order event
                        if (targetElement.id === 'orderBookBtn') {
                            document.getElementById('noData').style.display = 'none';
                            let orderBookListsContainer = document.getElementById('orderBookLists');

                            orderBookLists.innerHTML += '\
                            <div class="orderBookRow">\
                            ' + selfbookOptions + '\
                            <div class="suppliersRows" id="supplierRows_' + counter + '" data-counter="' + counter + '"></div>\
                            </div>';

                            counter++;
                        }

                        // If remove button is clicked
                        if (targetElement.classList.contains('removeOrderBtn')) {
                            let orderRow = targetElement.closest('div.orderBookRow');

                            if (orderRow) {
                                // Remove element.
                                orderRow.remove();
                            }
                        }

                    });

                    document.addEventListener('change', function(e) {
                        var targetElement = e.target; // Target element
                        var classList = targetElement.classList;

                        // Add suppliers row on book option change
                        if (classList.contains('bookNameSelect')) {
                            let bid = targetElement.value;

                            let counterId = targetElement
                                .closest('div.orderBookRow')
                                .querySelector('.suppliersRows')
                                .dataset.counter;


                            $.get('database/get-book-suppliers.php', {
                                id: bid
                            }, function(suppliers) {
                                vm.renderSupplierRows(suppliers, counterId);
                            }, 'json');
                        }
                    });
                },

                this.renderSupplierRows = function(suppliers, counterId) {
                    let supplierRows = '';

                    suppliers.forEach((supplier) => {
                        supplierRows += '\
                    <div class="row">\
                        <div style="width: 50%;">\
                            <p class="supplierName">' + supplier.supplier_name + '</p>\
                        </div>\
                        <div style="width: 50%;">\
                            <label for="quantity">Quantity: </label>\
                            <input type="number" class="appFormInput orderBookQty" id="quantity" placeholder="Enter quantity..." name="quantity[' + counterId + '][' + supplier.id + ']" />\
                        </div>\
                    </div>';
                    });

                    // Append to container
                    let supplierRowContainer = document.getElementById('supplierRows_' + counterId);
                    supplierRowContainer.innerHTML = supplierRows;
                }
        }

        (new Script()).initialize();
    </script>

</body>

</html>