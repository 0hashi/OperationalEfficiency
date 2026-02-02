<?php
// Office Work Stations
require_once __DIR__ . '/windows_helpers.php';

$officeInventory = [
    ['name' => 'tci-bt-eng01', 'ip' => 'tci-bt-eng01', 'comment' => 'Oscar\'s laptop (DHCP)'],
    ['name' => 'tci-bt-eng02', 'ip' => 'tci-bt-eng02', 'comment' => 'Matis\'s laptop (DHCP)'],
    ['name' => 'tci-bt-it02', 'ip' => '10.100.104.174', 'comment' => 'Paul\'s laptop'],
    ['name' => 'tci-bt-qa01', 'ip' => 'tci-bt-qa01', 'comment' => 'Full Tester (DHCP)'],
    ['name' => 'tci-bt-qa03', 'ip' => 'tci-bt-qa03', 'comment' => 'Diana\'s laptop (DHCP)'],
];

$officeWorkstations = [];

foreach ($officeInventory as $system) {
    $resolvedIp = resolveHost($system['ip']);
    $ping = pingSystem($resolvedIp);

    $officeWorkstations[] = [
        'name' => $system['name'],
        'ip' => $resolvedIp,
        'online' => $ping['online'],
        'latency' => $ping['latency'],
        'statusText' => $ping['online'] ? 'Online' : 'Offline',
	'comment'    => $system['comment'] ?? ''
    ];
}

