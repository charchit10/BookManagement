<?php
//Start the session.
session_start();
if (!isset($_SESSION['user'])) header('location: login.php');
$_SESSION['table'] = 'books';
$_SESSION['redirect_to'] = 'book-add.php';


$user = ($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book - Book Store Management System</title>

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
                            <h1 class="section_header"> <i class="fa fa-plus"></i> Add Book</h1>
                            <div id="userAddFormContainer">
                                <form action="database/add.php" method="POST" class="appForm" enctype="multipart/form-data">
                                    <div class="appFormInputContainer">
                                        <label for="book_name">Book Name</label>
                                        <input type="text" class="appFormInput" id="book_name" placeholder="Enter book name..." name="book_name" />
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="description">Description</label>
                                        <textarea class="appFormInput bookTextAreaInput" placeholder="Enter book description..." id="description" name="description">
                                    </textarea>
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="description">Suppliers</label>
                                        <select name="suppliers[]" id="suppliersSelect" multiple="">
                                            <option value="">Select Supplier</option>
                                            <?php
                                            $show_table = 'suppliers';
                                            $suppliers = include('database/show.php');

                                            foreach ($suppliers as $supplier) {
                                                echo "<option value=' " . $supplier['id'] . "'> " . $supplier['supplier_name'] . "</option>";
                                            };
                                            ?>
                                        </select>
                                    </div>
                                    <div class=" appFormInputContainer">
                                        <label for="book_name">Book Image</label>
                                        <input type="file" name="img" />
                                    </div>

                                    <button type="submit"><i class="fa fa-plus"></i> Create Book </button>
                                </form>
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
    </div>
    <?php include('partials/app-scripts.php'); ?>

</body>

</html>