<?php
// Load data sources
require_once __DIR__ . '/pollbreakroomwindowssystems.php';
require_once __DIR__ . '/pollextruderworkstations.php';
require_once __DIR__ . '/pollzebraprinters.php';
require_once __DIR__ . '/pollbraidingwindowssystems.php';
require_once __DIR__ . '/pollcablingwindowssystems.php';
require_once __DIR__ . '/pollcoilingwindowssystems.php';
require_once __DIR__ . '/pollofficeworkstations.php';

// Set timezone (adjust if needed)
date_default_timezone_set('America/Chicago');

// Capture page load time
$lastUpdated = date('m-d-Y H:i');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>TCI Systems Monitor</title>


<meta http-equiv="refresh" content="60">

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f2f4f7;
    margin: 0;
    padding: 20px;
}

.page-title {
    text-align: center;
    font-size: 32px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #1f2937;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    margin-bottom: 25px;
}

.last-updated {
    text-align: center;
    font-size: 16px;
    color: #374151;
    margin-top: -12px;
    margin-bottom: 20px;
    font-style: bold;
}

.section-title {
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    padding: 3px;
    background-color: #e5e7eb;
    box-shadow: 0 3px 6px rgba(0,0,0,0.25);
    border-radius: 6px;
    margin: 0px 0 5px 0;
}

.dashboard {
    display: grid;
    grid-template-columns: repeat(auto-fill, 250px);
    justify-content: start; /* or center */
    gap: 15px;
}

.printer-card {
    background-color: #ffffff;
    border-radius: 6px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    padding: 10px;
    font-size: 14px;
    margin: 3px 0;
    line-height: 1.2;
}

.printer-card p {
    margin: 2px 0;
    font-size: 13px;
}

.printer-header {
    padding: 7px 7px;
    font-size: 15px;
    font-weight: bold;
    border-radius: 4px;
    margin-bottom: 6px;
    color: #ffffff;
}

.status-idle { background-color: #28a745; }
.status-printing { background-color: #007bff; }
.status-error { background-color: #dc3545; }
.status-offline { background-color: #ff0000; }

.label {
    font-weight: bold;
}

/* Zebra printer comment styling */
.printer-comment {
    margin-top: 6px;
    padding: 4px 6px;
    background-color: #f9fafb;
    border-left: 3px solid #9ca3af;
    font-size: 0.85em;
    color: #374151;
}
</style>
</head>

<body>
<div class="page-title">TCI Systems Monitor</div>
<div class="last-updated">
    Last updated: <?= $lastUpdated ?>
</div>

<table width="100%" cellspacing="15">
<tr>

<!-- Braiding -->
<td valign="top" width="25%">
    <div class="section-title">Braiding Systems</div>
    <div class="dashboard">
        <?php foreach ($braidingSystems as $system): ?>
            <?php $statusClass = $system['online'] ? 'status-idle' : 'status-offline'; ?>
            <div class="printer-card">
                <div class="printer-header <?= $statusClass ?>">
                    <?= htmlspecialchars($system['name']) ?>
                </div>
                <p><span class="label">IP:</span> <?= $system['ip'] ?></p>
                <p><span class="label">Status:</span> <?= $system['statusText'] ?></p>

                <?php if ($system['latency']): ?>
                    <p><span class="label">Ping:</span> <?= $system['latency'] ?> ms</p>
                <?php endif; ?>

		<?php if (!empty($system['comment'])): ?>
                    <p class="printer-comment">
                        <span class="label">Note:</span>
                        <?= htmlspecialchars($system['comment']) ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</td>

<!-- Breakroom -->
<td valign="top" width="25%">
    <div class="section-title">Breakroom Systems</div>
    <div class="dashboard">
        <?php foreach ($breakroomSystems as $system): ?>
            <?php $statusClass = $system['online'] ? 'status-idle' : 'status-offline'; ?>
            <div class="printer-card">
                <div class="printer-header <?= $statusClass ?>">
                    <?= htmlspecialchars($system['name']) ?>
                </div>
                <p><span class="label">IP:</span> <?= $system['ip'] ?></p>
                <p><span class="label">Status:</span> <?= $system['statusText'] ?></p>

                <?php if ($system['latency']): ?>
                    <p><span class="label">Ping:</span> <?= $system['latency'] ?> ms</p>
                <?php endif; ?>

		<?php if (!empty($system['comment'])): ?>
                    <p class="printer-comment">
                        <span class="label">Note:</span>
                        <?= htmlspecialchars($system['comment']) ?>
                    </p>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>
</td>

<!-- Cabling -->
<td valign="top" width="25%">
    <div class="section-title">Cabling/Twinning/Cloud Test Systems</div>
    <div class="dashboard">
        <?php foreach ($cablingSystems as $system): ?>
            <?php $statusClass = $system['online'] ? 'status-idle' : 'status-offline'; ?>
            <div class="printer-card">
                <div class="printer-header <?= $statusClass ?>">
                    <?= htmlspecialchars($system['name']) ?>
                </div>
                <p><span class="label">IP:</span> <?= $system['ip'] ?></p>
                <p><span class="label">Status:</span> <?= $system['statusText'] ?></p>

                <?php if ($system['latency']): ?>
                    <p><span class="label">Ping:</span> <?= $system['latency'] ?> ms</p>
                <?php endif; ?>

		<?php if (!empty($system['comment'])): ?>
                    <p class="printer-comment">
                        <span class="label">Note:</span>
                        <?= htmlspecialchars($system['comment']) ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</td>

<!-- Coiling -->
<td valign="top" width="25%">
    <div class="section-title">Coiling/Spolling Systems</div>
    <div class="dashboard">
        <?php foreach ($coilingSystems as $system): ?>
            <?php $statusClass = $system['online'] ? 'status-idle' : 'status-offline'; ?>
            <div class="printer-card">
                <div class="printer-header <?= $statusClass ?>">
                    <?= htmlspecialchars($system['name']) ?>
                </div>
                <p><span class="label">IP:</span> <?= $system['ip'] ?></p>
                <p><span class="label">Status:</span> <?= $system['statusText'] ?></p>

                <?php if ($system['latency']): ?>
                    <p><span class="label">Ping:</span> <?= $system['latency'] ?> ms</p>
                <?php endif; ?>

		<?php if (!empty($system['comment'])): ?>
                    <p class="printer-comment">
                        <span class="label">Note:</span>
                        <?= htmlspecialchars($system['comment']) ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</td>
</tr>

<!-- Extruder workstations and work centers -->
<tr>
<td valign="top" width="28%">
    <div class="section-title">Extruder Workstations & Work Centers</div>
    <div class="dashboard">
        <?php foreach ($extruderWorkstations as $system): ?>
            <?php $statusClass = $system['online'] ? 'status-idle' : 'status-offline'; ?>
            <div class="printer-card">
                <div class="printer-header <?= $statusClass ?>">
                    <?= htmlspecialchars($system['name']) ?>
                </div>
                <p><span class="label">IP:</span> <?= $system['ip'] ?></p>
                <p><span class="label">Status:</span> <?= $system['statusText'] ?></p>

                <?php if ($system['latency']): ?>
                    <p><span class="label">Ping:</span> <?= $system['latency'] ?> ms</p>
                <?php endif; ?>

		<?php if (!empty($system['comment'])): ?>
                    <p class="printer-comment">
                        <span class="label">Note:</span>
                        <?= htmlspecialchars($system['comment']) ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</td>

<!-- Office -->
<td valign="top" colspan="3">
    <div class="section-title">Office Systems</div>
    <div class="dashboard">
        <?php foreach ($officeWorkstations as $system): ?>
            <?php $statusClass = $system['online'] ? 'status-idle' : 'status-offline'; ?>
            <div class="printer-card">
                <div class="printer-header <?= $statusClass ?>">
                    <?= htmlspecialchars($system['name']) ?>
                </div>
                <p><span class="label">IP:</span> <?= $system['ip'] ?></p>
                <p><span class="label">Status:</span> <?= $system['statusText'] ?></p>

                <?php if ($system['latency']): ?>
                    <p><span class="label">Ping:</span> <?= $system['latency'] ?> ms</p>
                <?php endif; ?>

                <?php if (!empty($system['comment'])): ?>
                    <p class="printer-comment">
                        <span class="label">Note:</span>
                        <?= htmlspecialchars($system['comment']) ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</td>
</table>

<!-- Zebra Printers -->
<div class="section-title">Zebra Printers</div>

<div class="dashboard">
<?php foreach ($printers as $printer): ?>

<?php
    $ip = $printer['ip'];
    $sysName = $printer['sysName'] ?? 'Unknown';
    $sysDescr = $printer['sysDescr'] ?? 'N/A';
    $statusText = $printer['statusText'] ?? 'Unknown';
    $errors = $printer['errors'] ?? [];
    $subnet = $printer['subnet'] ?? 'N/A';
    $gateway = $printer['gateway'] ?? 'N/A';
    $isOffline = $printer['offline'] ?? false;

    if ($isOffline) {
        $statusClass = 'status-offline';
        $statusText = 'Offline';
    } elseif (!empty($errors)) {
        $statusClass = 'status-error';
    } else {
        $statusClass = 'status-idle';
    }
?>

<div class="printer-card">
    <div class="printer-header <?= $statusClass ?>">
        <?= htmlspecialchars($sysName) ?>
    </div>

    <p><span class="label">IP Address:</span> <?= $ip ?></p>
    <p><span class="label">Subnet Mask:</span> <?= $subnet ?></p>
    <p><span class="label">Gateway:</span> <?= $gateway ?></p>
    <p><span class="label">Model:</span> <?= htmlspecialchars($sysDescr) ?></p>
    <p><span class="label">Status:</span> <?= $statusText ?></p>
    <p>
        <span class="label">Errors:</span>
        <?= !empty($errors) ? implode(', ', $errors) : ($isOffline ? 'Offline' : 'None') ?>
    </p>

    <?php if (!empty($printer['comment'])): ?>
        <p class="printer-comment">
            <span class="label">Note:</span>
            <?= htmlspecialchars($printer['comment']) ?>
        </p>
    <?php endif; ?>
</div>

<?php endforeach; ?>
</div>

<div class="section-title">Brother Printers</div>
</body>
</html>

