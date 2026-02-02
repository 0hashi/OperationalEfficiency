<?php
// Office Work Stations
require_once __DIR__ . '/windows_helpers.php';

$officeInventory = [
    ['name' => 'accounting-1', 'ip' => 'accounting-1', 'comment' => 'Jacob\'s laptop (DHCP)'],
    ['name' => 'jim-a', 'ip' => 'jim-a', 'comment' => 'Jim\'s laptop (DHCP)'],
    ['name' => 'tci-bt-adm01', 'ip' => 'tci-bt-adm01', 'comment' => 'Erika\'s Desktop (DHCP)'],
    ['name' => 'tci-bt-adm02', 'ip' => 'tci-bt-adm02', 'comment' => 'Jimmy\'s Desktop (DHCP)'],
    ['name' => 'tci-bt-eng01', 'ip' => 'tci-bt-eng01', 'comment' => 'Oscar\'s laptop (DHCP)'],
    ['name' => 'tci-bt-eng02', 'ip' => 'tci-bt-eng02', 'comment' => 'Matis\'s laptop (DHCP)'],
    ['name' => 'tci-bt-hr02', 'ip' => 'tci-bt-hr02', 'comment' => 'Lisa\'s laptop (DHCP)'],
    ['name' => 'maintenance-manager', 'ip' => 'maintenance-manager', 'comment' => 'Chad\'s laptop (DHCP)'],
    ['name' => 'production-manager', 'ip' => '10.100.104.223', 'comment' => 'Billy\'s laptop (DHCP)'],
    ['name' => 'sales-marketing', 'ip' => 'sales-marketing', 'comment' => 'Steve\'s laptop (DHCP)'],
    ['name' => 'shipping', 'ip' => 'shipping', 'comment' => 'Harley\'s laptop (DHCP)'],
    ['name' => 'tci-bt-ops01', 'ip' => 'tci-bt-ops01', 'comment' => 'Jim\'s laptop (DHCP)'],
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

