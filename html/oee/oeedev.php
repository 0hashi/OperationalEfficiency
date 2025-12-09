<?php
# PHP script: oee.php
# Paul Ohashi
# Trans Cable International
# Started: July 2025
#
# This script is one part of an overall full-stack package of technologies including:
#
# * MySQL database (oee) to store the data - This may be obsolete as I'm now pulling data directly
#	from rubicon.transcableusa.com - Maria DB.
# * Python script to unpack a zipped up file containing a bunch of Rubicon reports
#   and insert each report into its own database tables (only one table at the moment 'labor_efficiency').
# * Need to pull data from Rubicon then insert it into DB instead of extracting from xlsx reports.
# * IIS Web Site to display the data
#
# Need some error reporting...yes, of course, because this tangled web of tech is madness...
#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);

require_once 'oeeFunctions.php';
require_once 'oeeTestFunctions.php';
?>
<html>
<head>
    <title>TOEE</title>
	<!-- Load the chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- A pointer to CSS that makes it pretty. -->
	<link rel="stylesheet" href="newstyledev.css">
	<!-- Real time updates on dashboard -->
	<meta http-equiv="refresh" content="300">

	<style>
    /* Remove scrollbars */
    html, body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;   /* hides both horizontal and vertical scrollbars */
        overflow-y:
        height: 100%;
        width: 100%;
        background-color: #ffffff
    }

    /* Ensure tables shrink to fit inside viewport */
    table {
        max-width: 100%;
        max-height: 100%;
        border-collapse: collapse;
    }
    </style>

</head>
<body background=#ffffff>
<div style="display: flex; align-items: center;">
    <img src="pics/TransCableLogo.png" style="height: 60px; margin-right: 10px;">
    <h2 style="
        flex: 1;
        text-align: center;
        font-family: 'Poppins', sans-serif;
        font-size: 2.0em;
        background: linear-gradient(to right, #0077ff, #00c3ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
        letter-spacing: 1px;
        margin: 0;
    ">
        TCI Operational Efficiency
    </h2>
</div>
    <table style="width: 100%; max-width: 100%; border: 0px solid #fff;">
        <tr>
            <!-- Left Column: Navigation table -->
            <td style="width: 1%; vertical-align: top;">
                <!-- Navigation Links (Main, Quantity, Operations)-->
                <table style="
                    border-collapse: collapse;
                    border: 1px solid #fff;
                    box-shadow:
                    -8px 0 10px -5px rgba(0, 123, 255, 0.6),  /* Left glow */
                    0 8px 10px -5px rgba(0, 123, 255, 0.6);   /* Bottom glow */">
                    <thead>
                        <tr>
                            <th style="width: 100%; border: 1px solid #fff;">
                    <?php echo displayClock();?>
                    <a href="http://tci-bt-linux01/oee/oee.php" style="text-decoration: none; color: #36A2EB;">Main</a>
                    <br><hr color=#ddecf0>
                    <a href="http://tci-bt-linux01/oee/quantByEmployee.php" style="text-decoration: none; color: #36A2EB;">Quantity</a>
                    <br><hr color=#ddecf0>
                    <a href="http://tci-bt-linux01/oee/operations.php" style="text-decoration: none; color: #36A2EB;">Operations</a>
                    <hr color=#ddecf0>
							</th>
                        </tr>
                        <tr><td colspan=2 style="font-weight: bold; text-align: center; background-color: lightgrey; border: 2px solid #ddecf0;">Shift Aggregate</td></tr>
                        <tr>
                            <th style="width='50%'; width: 100%; border: 1px solid #fff;">
                                <strong><p><span title="
SELECT SUM(transaction_quantity) AS Quantity
FROM
    item_lrb_transactions
WHERE
    type = 'Manufacturing Receipt'
    AND created_date_time >= CURDATE() + INTERVAL 8 HOUR
    AND created_date_time <= CURDATE() + INTERVAL 16 HOUR">
                                <a href="http://tci-bt-linux01/oee/oeeoperatorefficiency.php" style="text-decoration: none; color: #000;">1st Shift</a>
                            </th>
                            <th style="width='50%'; width: 100%; border: 1px solid #fff;">
                                <?php
                                    $conn = connectRubiconTci();
                                    echo firstShiftTotalManufacturingReceiptQuantity($conn);
                                ?>
                            </th>
                        </tr>
                        <tr>
                            <th style="width='50%'; width: 100%; border: 1px solid #fff;">
                                <strong><p><span title="
SELECT SUM(transaction_quantity) AS Quantity
FROM
    item_lrb_transactions
WHERE
    type = 'Manufacturing Receipt'
    AND created_date_time >= (CURDATE() - INTERVAL 1 DAY) + INTERVAL 16 HOUR    # 4pm yesterday
    AND created_date_time <= CURDATE()  # Midnight last night">
                                <a href="http://tci-bt-linux01/oee/oeeoperatorefficiency.php" style="text-decoration: none; color: #000;">2nd Shift</a>
                            </th>
                            <th style="width='50%'; width: 100%; border: 1px solid #fff;">
                                <?php
                                    $conn = connectRubiconTci();
                                    echo secondShiftTotalManufacturingReceiptQuantity($conn);
                                ?>
                            </th>
                            <tr>
                            <th style="width='50%'; width: 100%; border: 1px solid #fff;">
                                <strong><p><span title="
SELECT SUM(transaction_quantity) AS Quantity
FROM
    item_lrb_transactions
WHERE
    type = 'Manufacturing Receipt'
    AND created_date_time >= CURDATE()                      # Midnight last night
    AND created_date_time <= CURDATE() + INTERVAL 8 HOUR    # 8am today">
                                <a href="http://tci-bt-linux01/oee/oeeoperatorefficiency.php" style="text-decoration: none; color: #000;">3rd Shift</a>
                            </th style="width='50%'; width: 100%; border: 1px solid #fff;">
                            <th>
                                <?php
                                    $conn = connectRubiconTci();
                                    echo thirdShiftTotalManufacturingReceiptQuantity($conn);
                                ?>
                            </th>
                        </tr>
                        </tr>
                    </thead>
                </table>
            </td>

            <!-- Table of Operators -->
            <td style="width: 60%; align=left; text-align=left">
                <!-- Table with Operators, 24, 7, 30 production days -->
                <table style="width: 100%; max-width: 100%; border: 0px solid #fff;">
                    <tbody style="text-align: left;">
                        <strong></strong>
					</tbody>
                <!-- Operator list with quarterly numbers -->
                <th style="width: 200px; border: 1px solid #fff;">
<p><span title="
Top Operators – A descending list of operators ranked by performance.
The list shows the total quantity of closed LRBs produced by each operator
during the current quarter. An LRB is considered closed when its type is
'Manufacturing Receipt'.

displayQuarterlyTransactionTotals($conn)
		               ">
                    <?php
						$conn = connectRubiconTci();
						displayQuarterlyTransactionTotals($conn);
					?>
				</th>
                <th style="text-align: center;">
                <table style="width: 100%; max-width: 100%; border: 0px solid #fff;">
                    <tr>
                        <td colspan=2 style="text-align: center; background-color: lightgrey;">
                            <p><span title="
LRB Manufacturing Receipt:
Today -  Total production from closed LRBs since 00:00 today.
7 Day -  Total production from closed LRBs since 00:00 seven days ago.
30 Day - Total production from closed LRBs since 00:00 30 days ago.
                            ">
                            <strong>LRB Manufacturing Receipt</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #ddecf0;">
                            <strong><p><span title="
SELECT SUM(transaction_quantity) AS total_quantity
FROM
    v_lrb_transactions
WHERE
    transaction_type = 'Manufacturing Receipt'
AND
    transaction_date >= CURDATE()
AND
    transaction_date <= NOW()">
                            <a href="lrbManufacturingReceipt.php?when=today" style="text-decoration: none; color: #000;">Today</a>
                            </strong> <!-- (<?php echo date("m/d/y"); ?>)</span></p> -->
                        </td>
                        <td style="background-color: #ddecf0;">
                    <?php
                        $conn = connectRubiconTci();
                        total_output_today($conn);
                    ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong><p><span title="
SELECT SUM(transaction_quantity) AS total_quantity
FROM
    v_lrb_transactions
WHERE
    transaction_type = 'Manufacturing Receipt'
AND
    transaction_date >= (CURDATE() - INTERVAL 7 DAY)
AND
    transaction_date <= NOW()">
                        <a href="lrbManufacturingReceipt.php?when=7day" style="text-decoration: none; color: #000;">
                            7 day</a></span></p></strong></td>
                        <td>
                    <?php
                        $conn = connectRubiconTci();
                        total_output_seven_days($conn);
                    ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #ddecf0;">
                            <strong><p><span title="
SELECT SUM(transaction_quantity) AS total_quantity
FROM
    v_lrb_transactions
WHERE
    transaction_type = 'Manufacturing Receipt'
AND
    transaction_date >= (CURDATE() - INTERVAL 30 DAY)
AND
    transaction_date <= NOW()
                            ">
                            <a href="lrbManufacturingReceipt.php?when=30day" style="text-decoration: none; color: #000;">
                            30 day</a></strong></p></td>
                        <td style="background-color: #ddecf0;">
                    <?php
                        $conn = connectRubiconTci();
                        total_output_thirty_days($conn);
                    ?>
                        </td>
                    </tr>
                <tr>
                        <td style="background-color: ; font-weight: bold;">
                            <a href="lrbManufacturingReceipt.php?when=thisMonth" style="text-decoration: none; color: #000;">
                            <?php
                                $currentMonth = date('F');
                                echo "$currentMonth";
                            ?>
                            </a>
                            </strong></p></td>
                        <td style="background-color: ;">
                    <?php
                        $conn = connectRubiconTci();
                        total_output_this_month($conn);
                    ?>
                        </td>
                    </tr>
                </th>
                    <!-- Table for the pie chart -->
                <tr>
                <td colspan=2 style="text-align: center;">
                    <hr>
                    <?php # Query Maria for the open orders - Pie chart
                        $conn = connectRubiconTci();
                        $result = $conn->query("SELECT status, COUNT(*) as count FROM v_sos WHERE open = '1' GROUP BY status");

                        $labels = [];
                        $data = [];

                        while ($row = $result->fetch_assoc()) {
                            $labels[] = $row['status'];
                            $data[] = $row['count'];
                        }
                        $conn->close();
                    ?>
                    <!-- Load Chart.js for open orders pie chart -->
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <!-- Data Labels Plugin -->
                    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

                        <div style="width: 250px; height: 250px; border: 0px solid #eee; margin: 0 auto;">
                            <canvas id="statusChart" style="display: block; margin: auto;"></canvas>
                        </div>
                </td>
                </tr>
                    <td colspan=2">
                        <hr>
                        <table style="width: 100%; max-width: 100%; border: 0px solid #fff;">
                            <tr>
                                <td>
                                    <?php
                                        $conn = connectRubiconTci();
                                        echo indirectLaborWorkCentersDaily($conn);
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
               </table>
    <script>
        const links = {
            'Canceled':             'openOrderStatus.php?report=canceledOrders',
            'Completed':            'openOrderStatus.php?report=completedOrders',
            'Partially Shipped':    'openOrderStatus.php?report=partiallyShipped',
            'Picking':              'openOrderStatus.php?report=pickingOpenOrders',
            'Open':                 'openOrderStatus.php?report=openOpenOrders',
            'On Hold':              'openOrderStatus.php?report=onHoldOperOrders'
        };
        const ctx = document.getElementById('statusChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'pie',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                        datasets: [{
                            data: <?php echo json_encode($data); ?>,
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#8BC34A']
                        }]
                        },
                        options: {
                            responsive: true,
                            onClick: function (evt, elements) {
                            if (elements.length > 0) {
                                const chartIndex = elements[0].index;
                                const label = chart.data.labels[chartIndex];
                                const url = links[label];
                                    if (url) {
                                        window.location.href = url;
                                    }
                            }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                title: {
                                    display: true,
                                    text: 'Status of Open Orders'
                                },
                                datalabels: {
                                    color: '#000',
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    },
                                    formatter: function(value) {
                                                return value;
                                    }
                                }
                            }
                        },
                            plugins: [ChartDataLabels]
                        });
    </script>
                </th>

                <th>
                    <!-- OE Work Center Output table -->
                    <div>
                        <table style="width: 100%; max-width: 100%; border: 0px solid #fff;">
                            <tr style="background-color: lightgrey;">
                                <td colspan=5 style="font-weight: bold; font-size: 18px; text-align: center;
                                border: 0px solid; border-collapse: collapse;">
                                <!-- Tool tip-->
                                <p><span title="
Black >= 100%
Amber >= 75% < 100%
Red < 75%
                             ">
                            <?php echo date('l, m/d'); ?></strong>
                                </p>
                                        <?php
                                            $fontSize = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : '12px';
                                            $today = (new DateTime())->modify('-0 day')->format('Y-m-d');
                                        ?>
                                        <!-- p style="font-size: <?= $fontSize ?>;">
                                            <?= htmlspecialchars($today) ?>
                                        </p -->
                                </td>
                            </tr>
                            <tr style="background-color: lightgrey; font-weight: bold; font-size: 16px;">
                                <td width='50%' style="text-align: center;">Work Center</td>
                                <td width='50%' style="text-align: center;">
                                    <p><span title="
Output: Total transaction quantity of all closed LRBs (transaction
type of 'Manufacturing Receipt') since midnight.
                            ">
                                    1st</td><td style="text-align: center;">2nd</td><td style="text-align: center;">3rd</td>
                            </tr>
                                <?php
                                    $conn = connectRubiconTci();
                                    echo workCenterOutput($conn);
                                ?>
                            <?php echo poweredBy();?>
                        </table>
                    </div>
                </th>
				</table>
            </td>
        </tr>
<!-- TICKER -->
        <tr>
            <td>
                <div class="ticker-wrap">
                    <div class="ticker" id="ticker">Loading ticker...</div>
                </div>

               <!-- CSS for ticker -->
                <style>
                    .ticker-wrap {
                      position: fixed;
                      bottom: 0;
                      width: 100%;
                      background: #222;
                      color: #fff;
                      overflow: hidden;
                      height: 40px;
                      display: flex;
                      align-items: center;
                      font-size: 16px;
                      font-weight: bold;
                      border-top: 2px solid #444;
                      z-index: 1000; /* ensures ticker is on top */
                    }

                    .ticker {
                      display: inline-block;
                      white-space: nowrap;
                      padding-left: 100%;
                      animation: ticker 100s linear infinite;
                    }

                    @keyframes ticker {
                      0%   { transform: translateX(0); }
                      100% { transform: translateX(-100%); }
                    }
                    </style>

                    <!-- JavaScript to fetch and auto-refresh ticker -->
                    <script>
                    function refreshTicker() {
                        fetch('ticker.php')
                            .then(response => response.text())
                            .then(data => {
                                const ticker = document.getElementById('ticker');
                                //ticker.textContent = data; // for straight white font color
                                ticker.innerHTML = data;
                            })
                            .catch(err => console.error('Error loading ticker:', err));
                    }

                    // Initial load
                    refreshTicker();

                    // Refresh every 30 seconds
                    setInterval(refreshTicker, 30000);
                    </script>
            </td>
        </tr>
    </table>
</body>
</html>
