<?php
# PHP script: oee.php
# Paul Ohashi
# Trans Cable International
# Started: July 2025
#
# Displays OEE dashboard widgets: Pie chart (status) + Gauge (receipts)

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OEE Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        table {
            border-collapse: collapse;
            margin: 20px auto;
            width: 100%;
            max-width: 900px;
        }
        td {
            vertical-align: top;
            padding: 10px;
        }
        .card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background: #fff;
        }
    </style>
</head>
<body>

<table>
    <tr>
        <!-- Pie Chart -->
        <td class="card" style="width:50%;">
            <h3>Order Status</h3>
            <canvas id="statusChart" style="width:100%; max-width:300px; height:250px;"></canvas>
        </td>

        <!-- Gauge Chart -->
        <td class="card" style="width:50%;">
            <h3>Today’s Receipts</h3>
            <div id="gauge_div" style="width:300px; height:250px; margin:0 auto;"></div>
        </td>
    </tr>
</table>

<!-- Chart.js Pie Chart -->
<script>
const ctx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Open Orders', 'In Progress', 'Completed'],
        datasets: [{
            data: [12, 7, 5], // TODO: Replace with PHP values if dynamic
            backgroundColor: ['#FF6384', '#36A2EB', '#4BC0C0'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>

<!-- Google Charts Gauge -->
<script type="text/javascript">
google.charts.load('current', { packages: ['gauge'] });
google.charts.setOnLoadCallback(drawGauge);

function drawGauge() {
    var data = google.visualization.arrayToDataTable([
        ['Label', 'Value'],
        ['Receipts', 85] // TODO: Replace with PHP value if dynamic
    ]);

    var options = {
        width: 300, height: 250,
        redFrom: 0, redTo: 30,
        yellowFrom: 30, yellowTo: 70,
        greenFrom: 70, greenTo: 100,
        minorTicks: 5,
        max: 100
    };

    var chart = new google.visualization.Gauge(document.getElementById('gauge_div'));
    chart.draw(data, options);
}
</script>

</body>
</html>
