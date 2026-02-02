<?php
require_once __DIR__ . '/windows_helpers.php';

$braidingInventory = [
    ['name' => 'tci-bt-brd01', 'ip' => '10.100.105.5', 'comment' => 'Braider workstation'],
];

$braidingSystems = [];

foreach ($braidingInventory as $system) {
    $resolvedIp = resolveHost($system['ip']);
    $ping = pingSystem($resolvedIp);

    $braidingSystems[] = [
        'name' => $system['name'],
        'ip' => $resolvedIp,
        'online' => $ping['online'],
        'latency' => $ping['latency'],
        'statusText' => $ping['online'] ? 'Online' : 'Offline',
	'comment'	=> $system['comment'] ?? ''
    ];
}

