<?php
require_once __DIR__ . '/windows_helpers.php';

$spoolingInventory = [
    ['name' => 'tci-bt-spool01', 'ip' => '10.100.105.17', 'comment' => 'Test'],
];

$spoolingSystems = [];

foreach ($spoolingInventory as $system) {
    $resolvedIp = resolveHost($system['ip']);
    $ping = pingSystem($resolvedIp);

    $spoolingSystems[] = [
        'name' => $system['name'],
        'ip' => $resolvedIp,
        'online' => $ping['online'],
        'latency' => $ping['latency'],
        'statusText' => $ping['online'] ? 'Online' : 'Offline',
	'comment'	=> $system['comment'] ?? '',
    ];
}

