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
?>
<html>
<head>
    <title>TOEE</title>
	<!-- Load the chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- A pointer to CSS that makes it pretty. -->
	<link rel="stylesheet" href="style.css">
	<!-- Real time updates on dashboard -->
	<meta http-equiv="refresh" content="60">
</head>
<body>
<h2 style="
    text-align: center;
    font-family: 'Poppins', sans-serif;
    font-size: 2.0em;
    background: linear-gradient(to right, #0077ff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
    letter-spacing: 1px;
">
    TCI Operational Efficiency
</h2>
    <?php echo displayClock();?>
    <table style="width: 100%; border: 1px;">
        <tr>
            <!-- Left Column: Data Table -->
            <td style="width: 10%; vertical-align: top;">
                <table style="border: 1px;">
				     <tbody>
                    </tbody>
                    <thead>
                        <tr>
                            <th>
                    <a href="http://tci-bt-linux01/oee/oee.php" style="text-decoration: none; color: #36A2EB;">Main</a>
                    <br><hr color=lightblue>
                    <a href="http://tci-bt-linux01/oee/quantByEmployee.php" style="text-decoration: none; color: #36A2EB;">Quantity</a>
                    <br><hr color=lightblue>
                    <a href="http://tci-bt-linux01/oee/operations.php" style="text-decoration: none; color: #36A2EB;">Operations</a>
                    <hr color=lightblue>
							</th>
                        </tr>
                    </thead>
                </table>
            </td>

            <!-- Right Column -->
            <td style="width: 60%; align=left; text-align=left">
                <table style="width: 100%;">
                    <tbody style="text-align: left;">
                        <strong></strong>
					</tbody>
                <th width=30%>
                    <?php
						$conn = connectRubiconTci();
						activeOperators($conn);
					?>
				</th>
                <th>
                <hr color=lightblue> <!-- Upper blue <hr> -->
                <table>
                    <tr>
                        <td><strong>24 hour production:</td>
                        <td>
                    <?php
                        $conn = connectRubiconTci();
                        total_output_today($conn);
                    ?>
                    Ft.
                        </td>
                    </tr>
                    <tr>
                        <td><strong>7 day production:</td>
                        <td>
                    <?php
                        $conn = connectRubiconTci();
                        total_output_seven_days($conn);
                    ?>
                    Ft.
                        </td>
                    </tr>
                    <tr>
                        <td><strong>30 day production:</td>
                        <td>
                    <?php
                        $conn = connectRubiconTci();
                        total_output_thirty_days($conn);
                    ?>
                    Ft.
                        </td>
                    </tr>
                </table>
                <hr color=lightblue> <!-- LOWER blue <hr> -->

<?php # Query MariaDB for the open orders
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

    <div style="width: 250px; height: 250px;">
        <canvas id="statusChart"></canvas>
    </div>
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
                    <div>
                        <table width=100% style="border: 1px;">
                            <tr style="background-color: lightgrey;">
                                <td colspan=3 style="font-weight: bold; font-size: 18px; text-align: center;
                                border: 0px solid; border-collapse: collapse;">
                                        OE
                                </td>
                            </tr>
                            <tr style="background-color: lightgrey; font-weight: bold; font-size: 16px;">
                                <td width='50%' style="text-align: center;">Work Center</td><td width='50%' style="text-align: center;">Output</td>
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
</body>
</html>
