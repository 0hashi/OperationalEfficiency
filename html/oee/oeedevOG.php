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
	<link rel="stylesheet" href="style.css">
	<!-- Real time updates on dashboard -->
	<meta http-equiv="refresh" content="300">

</head>
<body>
<table style="width: 100px; border: 1px solid #ccc;">
    <tr><td>
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
    <table>
        <tr>
            <!-- Left Column: Navigation table -->
            <td style="vertical-align: top;">
                <!-- Navigation Links (Main, Quantity, Operations)-->
                <table style="
                    border-collapse: collapse;
                    border: 1px solid #ccc; <!-- ccc -->
                    box-shadow:
                    -8px 0 10px -5px rgba(0, 123, 255, 0.6),  /* Left glow */
                    0 8px 10px -5px rgba(0, 123, 255, 0.6);   /* Bottom glow */">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ccc;"> <!-- ccc -->
                    <?php echo displayClock();?>

                    <div class="scrollable">

                    <a href="http://tci-bt-linux01/oee/oee.php" style="text-decoration: none; color: #36A2EB;">Main</a>
                    <br><hr color=lightblue>
                    <a href="http://tci-bt-linux01/oee/quantByEmployee.php" style="text-decoration: none; color: #36A2EB;">Quantity</a>
                    <br><hr color=lightblue>
                    <a href="http://tci-bt-linux01/oee/operations.php" style="text-decoration: none; color: #36A2EB;">Operations</a>
                    <hr color=lightblue>
							</th>
                        </tr>
                        <tr><td colspan=2 style="font-weight: bold; text-align: center; background-color: lightgrey; border: 2px solid lightblue;">Shift Aggregate</td></tr>
                        <tr>
                            <th style="width='50%'; width: 100%; border: 1px solid #ccc;"> <!-- ccc -->
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
                            <th style="width='50%'; width: 100%; border: 1px solid #ccc;"> <!-- ccc -->
                                <?php
                                    $conn = connectRubiconTci();
                                    echo firstShiftTotalManufacturingReceiptQuantity($conn);
                                ?>
                            </th>
                        </tr>
                        <tr>
                            <th style="width='50%'; width: 100%; border: 1px solid #ccc;"> <!-- ccc -->
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
                            <th style="width='50%'; width: 100%; border: 1px solid #ccc;"> <!-- ccc -->
                                <?php
                                    $conn = connectRubiconTci();
                                    echo secondShiftTotalManufacturingReceiptQuantity($conn);
                                ?>
                            </th>
                            <tr>
                            <th style="width='50%'; width: 100%; border: 1px solid #ccc;"> <!-- ccc -->
                                <strong><p><span title="
SELECT SUM(transaction_quantity) AS Quantity
FROM
    item_lrb_transactions
WHERE
    type = 'Manufacturing Receipt'
    AND created_date_time >= CURDATE()                      # Midnight last night
    AND created_date_time <= CURDATE() + INTERVAL 8 HOUR    # 8am today">
                                <a href="http://tci-bt-linux01/oee/oeeoperatorefficiency.php" style="text-decoration: none; color: #000;">3rd Shift</a>
                            </th style="width='50%'; width: 100%; border: 1px solid #ccc;"> <!-- ccc -->
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

            <!-- Table on the right -->
            <td style="width: 60%; align=left; text-align=left">
                <!-- Table with Operators, 24, 7, 30 production days -->
                <table style="width: 100%; border: 1px solid #ccc;"> <!-- ccc -->
                    <tbody style="text-align: left;">
                        <strong></strong>
					</tbody>
                <!-- Operator list with quarterly numbers -->
                <th style="width: 200px; border: 1px solid #ccc;"> <!-- ccc -->
                <p><span title="
function displayQuarterlyTransactionTotals($conn) {

    SELECT
      user_name,
      FORMAT(total_qty, 2) AS total_quantity
    FROM (
      SELECT
        user_name,
        COALESCE(SUM(transaction_quantity), 0) AS total_qty
      FROM item_lrb_transactions
      WHERE type = 'Manufacturing Receipt'
        AND QUARTER(created_date_time) = QUARTER(CURDATE())
        AND YEAR(created_date_time) = YEAR(CURDATE())
      GROUP BY user_name

      UNION ALL

      SELECT
        'TOTAL' AS user_name,
        COALESCE(SUM(transaction_quantity), 0) AS total_qty
      FROM item_lrb_transactions
      WHERE type = 'Manufacturing Receipt'
        AND QUARTER(created_date_time) = QUARTER(CURDATE())
        AND YEAR(created_date_time) = YEAR(CURDATE())
    ) AS combined
    ORDER BY
      CASE WHEN user_name = 'TOTAL' THEN 2 ELSE 1 END,
      total_qty DESC,
      user_name ASC ">
                    <?php
						$conn = connectRubiconTci();
						displayQuarterlyTransactionTotals($conn);
					?>
				</th>
                <th style="text-align: center;">
                <table style="width='20%'; width: 100%; border: 1px solid #ccc;"> <!-- ccc -->
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
                        <td style="background-color: lightblue;">
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
                        <td style="background-color: lightblue;">
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
                        <td style="background-color: lightblue;">
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
                        <td style="background-color: lightblue;">
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
                        whatMonthIsIt($conn);
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
                    <td colspan=2>
                        <hr>
                        <table width=100%>
                            <tr style="background: lightgrey; font-weight: bold;">
                                <td colspan=4 style="text-align: center;">Indirect Labor Work Centers</td>
                            <tr style="background: lightgrey; font-weight: bold;">
                                <td>Work Center</td><td>Operator</td><td>PWO</td><td>Hours</td>
                            </tr>
                            <tr>
                                <td>
                                    <?php
                                        $conn = connectRubiconTci();
                                        #echo indirectLaborWorkCenters($conn);
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <hr>
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
                        <table style="width: 100%; border: 1px solid #ccc;"> <!-- ccc -->
                            <tr style="background-color: lightgrey;">
                                <td colspan=5 style="font-weight: bold; font-size: 18px; text-align: center;
                                border: 0px solid; border-collapse: collapse;">
                                <!-- Tool tip-->
                                <p><span title="
SELECT
    received_work_center,
    SUM(transaction_quantity) AS total_quantity
FROM
    v_lrb_transactions
WHERE
    transaction_type = 'Manufacturing Receipt'
    AND transaction_date = CURDATE()
GROUP BY
    received_work_center
ORDER BY
    received_work_center
                            ">
                                <?php
                                        # Figure out which shift is working now
                                        date_default_timezone_set('America/Chicago'); // Set to your timezone
                                        $currentHour = (int) date('G'); // 24-hour format without leading zeros (0–23)
                                        $firstShiftColor = 'black';
                                        $secondShiftColor = 'black';
                                        $thirdShiftColor = 'black';

                                        if ($currentHour >= 8 && $currentHour < 16) {
                                            $whichShift = "1st";
                                            $firstShiftColor = 'green';
                                        } elseif ($currentHour >= 16 && $currentHour < 24) {
                                            $whichShift = "2nd";
                                            $secondShiftColor = 'green';
                                        } else { // covers 00:00–07:59
                                            $whichShift = "3rd";
                                            $thirdShiftColor = 'green';
                                        }
                                ?>
                            Daily OE - <?php echo date('l, m/d'); ?></strong>
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
                                <td width='40%' style="text-align: center;">Work Center</td>
                                <!-- td width='15%' style="text-align: center; color: <?php echo $firstShiftColor ?>;">1st Shift</td>
                                <td width='15%' style="text-align: center; color: <?php echo $secondShiftColor ?>;">2nd Shift</td>
                                <td width='15%' style="text-align: center; color: <?php echo $thirdShiftColor ?>;">3rd Shift</td -->
                                <td width='15%' style="text-align: center;">
                                    <p><span title="
Output: Total transaction quantity of all closed LRBs (transaction
type of 'Manufacturing Receipt') since midnight.
                            ">
                                    Today</td>
                            </tr>
                                <?php
                                    $conn = connectRubiconTci();
                                    workCenterOutput($conn);
                                ?>
                            <?php echo poweredBy();?>
                        </table>
                    </div>
                </th>
				</table>
            </td>
        </tr>
    </table>
    </td>
    </tr>
</table>
</body>
</html>
