<?php
//Start the session.
session_start();
if (!isset($_SESSION['user'])) header('location: login.php');

$user = ($_SESSION['user']);

// Get graph data - purchasse order by status
include('database/bo_status_pie_graph.php');

// Get graph data - supplier book count
include('database/supplier_book_bar_graph.php');

// Get line graph data - delivery history per day
include('database/delivery_history.php');
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
            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <div class="col50">
                        <figure class="highcharts-figure">
                            <div id="container"></div>
                            <p class="highcharts-description">
                                Here is the breakdown of the purchase orders by status.
                            </p>
                        </figure>
                    </div>
                    <div class="col50">
                        <figure class="highcharts-figure">
                            <div id="containerBarChart"></div>
                            <p class="highcharts-description">
                                Here is the bar graph of the book counts assigned to suppliers.
                            </p>
                        </figure>
                    </div>
                </div>
                <div id="deliveryHistory">

                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>


    <script>
        var graphData = <?= json_encode($results) ?>;
        Highcharts.chart('container', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Purchase Orders By Status',
                align: 'left'
            },
            tooltip: {
                pointFormatter: function() {
                    var point = this,
                        series = point.series;

                    return `<b>${point.name}</b>: ${point.y}`
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y}'
                    }
                }
            },
            series: [{
                name: 'Status',
                colorByPoint: true,
                data: graphData
            }]
        });


        var barGraphData = <?= json_encode($bar_chart_data) ?>;
        var barGraphCategories = <?= json_encode($categories) ?>;


        console.log(barGraphData, barGraphCategories);

        Highcharts.chart('containerBarChart', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Book Count Assigned To Supplier'
            },
            xAxis: {
                categories: barGraphCategories,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Book Count'
                }
            },
            tooltip: {
                pointFormatter: function() {
                    var point = this,
                        series = point.series;
                    return `<b>${point.category}</b>: ${point.y}`
                }

            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Suppliers',
                data: barGraphData
            }]
        });

        var lineCategories = <?= json_encode($line_categories) ?>;
        var lineData = <?= json_encode($line_data) ?>;

        console.log(lineCategories);
        console.log(lineData);

        Highcharts.chart('deliveryHistory', {
            chart: {
                type: 'spline'
            },
            title: {
                text: 'Delivery History Per Day',
                align: 'left'
            },

            yAxis: {
                title: {
                    text: 'Book Delivered'
                }
            },

            xAxis: {
                categories: lineCategories
            },

            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },

            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                }
            },

            series: [{
                name: 'Book Delivered',
                data: lineData
            }],

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
        });
    </script>
</body>

</html>