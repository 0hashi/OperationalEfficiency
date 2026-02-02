<?php
require_once __DIR__ . '/windows_helpers.php';

$coilingInventory = [
    ['name' => 'tci-bt-coiler01', 'ip' => '10.100.104.145', 'comment' => 'Test'],
    ['name' => 'tci-bt-coiler02', 'ip' => '10.100.105.63', 'comment' => 'Test'],
    ['name' => 'tci-bt-spool01', 'ip' => '10.100.105.17', 'comment' => 'Test'],
];

$coilingSystems = [];

foreach ($coilingInventory as $system) {
    $resolvedIp = resolveHost($system['ip']);
    $ping = pingSystem($resolvedIp);

    $coilingSystems[] = [
        'name' => $system['name'],
        'ip' => $resolvedIp,
        'online' => $ping['online'],
        'latency' => $ping['latency'],
        'statusText' => $ping['online'] ? 'Online' : 'Offline',
	'comment' => $system['comment'] ?? ''
    ];
}

