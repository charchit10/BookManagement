<?php
//Start the session.
session_start();
if (!isset($_SESSION['user'])) header('location: login.php');
?>
<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSM Login - Book Store Management System</title>
    <link rel="stylesheet" type="text/css" href="style/style.css">
    <link rel="stylesheet" href="style/font-awesome/css/font-awesome.min.css">
</head>

<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php') ?>
        <div class="dashboard_content_container" id="dashboard_content_container">
            <?php include('partials/app-topnav.php') ?>
            <div id="reportsContainer">
                <div class="reportTypeContainer">
                    <div class="reportType">
                        <p>Export Books</p>
                        <div class="alignRight">
                            <a href="database/report_csv.php?report=book" class="reportExportBtn">Excel</a>
                            <a href="database/report_pdf.php?report=book" target="_blank" class="reportExportBtn">PDF</a>
                        </div>
                    </div>
                    <div class="reportType">
                        <p>Export Suppliers</p>
                        <div class="alignRight">
                            <a href="database/report_csv.php?report=supplier" class="reportExportBtn">Excel</a>
                            <a href="database/report_pdf.php?report=supplier" target="_blank" class="reportExportBtn">PDF</a>
                        </div>
                    </div>
                </div>
                <div class="reportTypeContainer">
                    <div class="reportType">
                        <p>Export Deliveries</p>
                        <div class="alignRight">
                            <a href="database/report_csv.php?report=delivery" class="reportExportBtn">Excel</a>
                            <a href="database/report_pdf.php?report=delivery" target="_blank" class="reportExportBtn">PDF</a>
                        </div>
                    </div>
                    <div class="reportType">
                        <p>Export Purchase Orders</p>
                        <div class="alignRight">
                            <a href="database/report_csv.php?report=purchase_orders" class="reportExportBtn">Excel</a>
                            <a href="database/report_pdf.php?report=purchase_orders" target="_blank" class="reportExportBtn">PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>

</html>