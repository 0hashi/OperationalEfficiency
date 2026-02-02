<?php
// Extruder Work Stations
require_once __DIR__ . '/windows_helpers.php';

$extruderInventory = [
    ['name' => 'tci-bt-e1', 'ip' => '10.100.105.32', 'comment' => 'E1 WS'],
    ['name' => 'E1-Line-Controller', 'ip' => '10.100.104.200', 'comment' => 'E1 WC'],
    ['name' => 'tci-bt-e2', 'ip' => '10.100.105.1', 'comment' => 'E2 WS'],
    
    ['name' => 'tci-bt-e3', 'ip' => '10.100.105.78', 'comment' => 'E3 WS'],
    ['name' => 'DP141006 (E3)', 'ip' => '10.100.104.196', 'comment' => 'E3 WC - Win7'],

    ['name' => 'tci-bt-e4e5', 'ip' => '10.100.105.50', 'comment' => 'E4/E5 WS'],

    ['name' => 'tci-bt-e6', 'ip' => '10.100.105.127', 'comment' => 'E6 WS'],
    ['name' => 'E6', 'ip' => '10.100.104.19', 'comment' => 'E6 WC'],
];

$extruderWorkstations = [];

foreach ($extruderInventory as $system) {
    $resolvedIp = resolveHost($system['ip']);
    $ping = pingSystem($resolvedIp);

    $extruderWorkstations[] = [
        'name' => $system['name'],
        'ip' => $resolvedIp,
        'online' => $ping['online'],
        'latency' => $ping['latency'],
        'statusText' => $ping['online'] ? 'Online' : 'Offline',
	'comment' => $system['comment'] ?? ''
    ];
}

