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
    <title>View Purchase Orders - Book Store Management System</title>
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
                            <h1 class="section_header"> <i class="fa fa-list"></i> List of Purchase Orders</h1>
                            <div class="section_content">
                                <div class="boListContainers">
                                    <?php
                                    $stmt = $conn->prepare("
                                    SELECT order_book.id, order_book.book, books.book_name, order_book.quantity_ordered, users.first_name, order_book.batch, order_book.quantity_received,
                                            users.last_name, suppliers.supplier_name, order_book.status, order_book.created_at
                                        FROM order_book, suppliers, books, users
                                        WHERE
                                            order_book.supplier = suppliers.id
                                                AND
                                            order_book.book = books.id
                                                AND
                                            order_book.created_by = users.id
                                        ORDER BY
                                            order_book.created_at DESC
                                            ");
                                    $stmt->execute();
                                    $purchase_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    $data = [];
                                    foreach ($purchase_orders as  $purchase_order) {
                                        $data[$purchase_order['batch']][] = $purchase_order;

                                    ?>

                                        <?php
                                        foreach ($data as $batch_id => $batch_bos) {
                                        }
                                        ?>
                                        <div class="boList" id="container-<?= $batch_id ?>">
                                            <p>Batch #: <?= $batch_id ?></p>
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Book</th>
                                                        <th>Qty Ordered</th>
                                                        <th>Qty Received</th>
                                                        <th>Supplier</th>
                                                        <th>Status</th>
                                                        <th>Ordered By</th>
                                                        <th>Created Date</th>
                                                        <th>Delivery History</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($batch_bos as $index => $batch_bo) {
                                                    ?>
                                                        <tr>
                                                            <td> <?= $index + 1 ?></td>
                                                            <td class="bo_book"><?= $batch_bo['book_name'] ?></td>
                                                            <td class="bo_qty_ordered"><?= $batch_bo['quantity_ordered'] ?></td>
                                                            <td class="bo_qty_received"><?= $batch_bo['quantity_received'] ?></td>
                                                            <td class="bo_qty_supplier"><?= $batch_bo['supplier_name'] ?></td>
                                                            <td class=" bo_qty_status"><span class="bo-badge bo-badge-<?= $batch_bo['status'] ?>"><?= $batch_bo['status'] ?></span></td>
                                                            <td><?= $batch_bo['first_name'] . ' ' . $batch_bo['last_name'] ?></td>
                                                            <td>
                                                                <?= $batch_bo['created_at'] ?>
                                                                <input type="hidden" class="bo_qty_row_id" value="<?= $batch_bo['id'] ?>">
                                                                <input type="hidden" class="bo_qty_bookid" value="<?= $batch_bo['book'] ?>">
                                                            </td>
                                                            <td>
                                                                <button class="appbtn appDeliveryHistory" data-id="<?= $batch_bo['id'] ?>"> Deliveries ></button>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                            <div class="boOrderUpdateBtnContainer alignRight">
                                                <button class="appbtn updateBoBtn" data-id="<?= $batch_id ?>">Update</button>
                                            </div>
                                        </div>

                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include('partials/app-scripts.php'); ?>
                <script>
                    function script() {
                        var vm = this;

                        this.registerEvents = function() {
                                document.addEventListener('click', function(e) {
                                    var targetElement = e.target;
                                    var classList = targetElement.classList;

                                    if (classList.contains('updateBoBtn')) {
                                        e.preventDefault();

                                        var batchNumber = targetElement.dataset.id;
                                        var batchNumberContainer = 'container-' + batchNumber;


                                        // Get all purchase order book records

                                        var bookList = document.querySelectorAll('#' + batchNumberContainer + ' .bo_book');
                                        var qtyOrderedList = document.querySelectorAll('#' + batchNumberContainer + ' .bo_qty_ordered');
                                        var qtyReceivedList = document.querySelectorAll('#' + batchNumberContainer + ' .bo_qty_received');
                                        var supplierList = document.querySelectorAll('#' + batchNumberContainer + ' .bo_qty_supplier');
                                        var statusList = document.querySelectorAll('#' + batchNumberContainer + ' .bo_qty_status');
                                        var rowIds = document.querySelectorAll('#' + batchNumberContainer + ' .bo_qty_row_id');
                                        var bIds = document.querySelectorAll('#' + batchNumberContainer + ' .bo_qty_bookid');



                                        boListsArr = [];

                                        for (i = 0; i < bookList.length; i++) {
                                            boListsArr.push({
                                                name: bookList[i].innerText,
                                                qtyOrdered: qtyOrderedList[i].innerText,
                                                qtyReceived: qtyReceivedList[i].innerText,
                                                supplier: supplierList[i].innerText,
                                                status: statusList[i].innerText,
                                                id: rowIds[i].value,
                                                bid: bIds[i].value
                                            });
                                        }

                                        // Store in HTML
                                        var boListHtml = '\
                                            <table id="formTable_' + batchNumber + '">\
                                                <thead>\
                                                    <tr>\
                                                        <th>Book Name</th>\
                                                        <th>Qty Ordered</th>\
                                                        <th>Qty Received</th>\
                                                        <th>Qty Delivered</th>\
                                                        <th>Supplier</th>\
                                                        <th>Status</th>\
                                                    </tr>\
                                                </thead>\
                                                <tbody>';

                                        boListsArr.forEach((boList) => {
                                            boListHtml += '\
                                                    <tr>\
                                                            <td class="bo_book alignLeft">' + boList.name + '</td>\
                                                            <td class="bo_qty_ordered">' + boList.qtyOrdered + '</td>\
                                                            <td class="bo_qty_received">' + boList.qtyReceived + '</td>\
                                                            <td class="bo_qty_delivered"><input type="number" value="0" /></td>\
                                                            <td class="bo_qty_supplier alignLeft">' + boList.supplier + '</td>\
                                                            <td>\
                                                                <select class="bo_qty_status">\
                                                                    <option value="pending" ' + (boList.status == 'pending' ? 'selected' : '') + '>pending</option>\
                                                                    <option value="incomplete" ' + (boList.status == 'incomplete' ? 'selected' : '') + '>incomplete</option>\
                                                                    <option value="complete" ' + (boList.status == 'complete' ? 'selected' : '') + '>complete</option>\
                                                                </select>\
                                                                <input type="hidden" class="bo_qty_row_id" value="' + boList.id + '">\
                                                                <input type="hidden" class="bo_qty_bid" value="' + boList.bid + '">\
                                                            </td>\
                                                        </tr>\
                                                        ';
                                        });
                                        boListHtml += '</tbody></table>';

                                        var bName = targetElement.dataset.name;

                                        BootstrapDialog.confirm({
                                            type: BootstrapDialog.TYPE_PRIMARY,
                                            title: 'Update Purchase Order: Batch #: <strong>' + batchNumber + '</strong>',
                                            message: boListHtml,
                                            callback: function(toAdd) {
                                                // If we add
                                                if (toAdd) {
                                                    var formTableContainer = 'formTable_' + batchNumber;

                                                    // Get all purchase order book records
                                                    var qtyReceivedList = document.querySelectorAll('#' + formTableContainer + ' .bo_qty_received');
                                                    var qtyDeliveredList = document.querySelectorAll('#' + formTableContainer + ' .bo_qty_delivered input');
                                                    var statusList = document.querySelectorAll('#' + formTableContainer + ' .bo_qty_status');
                                                    var rowIds = document.querySelectorAll('#' + formTableContainer + ' .bo_qty_row_id');
                                                    var qtyOrdered = document.querySelectorAll('#' + formTableContainer + ' .bo_qty_ordered');
                                                    var bids = document.querySelectorAll('#' + formTableContainer + ' .bo_qty_bid');


                                                    boListsArrForm = [];

                                                    for (i = 0; i < qtyDeliveredList.length; i++) {
                                                        boListsArrForm.push({
                                                            qtyReceive: qtyReceivedList[i].innerText,
                                                            qtyDelivered: qtyDeliveredList[i].value,
                                                            status: statusList[i].value,
                                                            id: rowIds[i].value,
                                                            qtyOrdered: qtyOrdered[i].innerText,
                                                            bid: bids[i].value
                                                        });
                                                    }

                                                    // Send request / update database
                                                    $.ajax({
                                                        type: 'POST',
                                                        dataType: 'json',
                                                        data: {
                                                            payload: boListsArrForm
                                                        },
                                                        url: 'database/update-order.php',
                                                        success: function(data) {
                                                            var message = data.message;

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
                                    }

                                    // If dedliveries btn is clicked
                                    if (classList.contains('appDeliveryHistory')) {
                                        let id = targetElement.dataset.id;

                                        $.get('database/view-delivery-history.php', {
                                            id: id
                                        }, function(data) {
                                            if (data.length) {
                                                rows = '';
                                                data.forEach((row, id) => {
                                                    receivedDate = new Date(row['date_received']);
                                                    rows += '\
                                                    <tr>\
                                                        <td>' + (id + 1) + '</td>\
                                                        <td>' + receivedDate.toUTCString() + '</td>\
                                                        <td>' + row['qty_received'] + '</td>\
                                                        </tr>\
                                                        <tr>';
                                                });

                                                deliveryHistoryHtml = '<table class="deliveryHistoryTable">\
                                                <thead>\
                                                    <tr>\
                                                        <th>#</th>\
                                                        <th>Date Received</th>\
                                                        <th>Quantity Received</th>\
                                                    </tr>\
                                                </thead>\
                                                    tbody>' + rows + '</tbody>\
                                                </table>';

                                                BootstrapDialog.show({
                                                    title: '<strong>Delivery History </strong>',
                                                    type: BootstrapDialog.TYPE_PRIMARY,
                                                    message: deliveryHistoryHtml
                                                });
                                            } else {
                                                BootstrapDialog.alert({
                                                    title: '<strong> No Delivery History </strong>',
                                                    type: BootstrapDialog.TYPE_INFO,
                                                    message: 'No delivery history found on selected book.'
                                                });
                                            }
                                        }, 'json');
                                    }
                                });
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