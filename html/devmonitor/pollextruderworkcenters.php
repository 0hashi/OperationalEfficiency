<?php
// Extruder Work Centers
require_once __DIR__ . '/windows_helpers.php';

$extruderWcInventory = [
    ['name' => 'E1-Line-Controller', 'ip' => '10.100.104.200', 'comment' => 'E1 WC'],
    ['name' => 'DP141006 (E3)', 'ip' => '10.100.104.196', 'comment' => 'Win7'],
    ['name' => 'E6', 'ip' => '10.100.104.19', 'comment' => 'E6 WC'],
];

$extruderWorkcenters = [];

foreach ($extruderWcInventory as $system) {
    $resolvedIp = resolveHost($system['ip']);
    $ping = pingSystem($resolvedIp);

    $extruderWorkcenters[] = [
        'name' => $system['name'],
        'ip' => $resolvedIp,
        'online' => $ping['online'],
        'latency' => $ping['latency'],
        'statusText' => $ping['online'] ? 'Online' : 'Offline',
	'comment'	=> $system['comment'] ?? ''
    ];
}

