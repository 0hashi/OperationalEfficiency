<?php
require_once __DIR__ . '/windows_helpers.php';

$cablingInventory = [
    ['name' => 'tci-bt-cab02', 'ip' => '10.100.104.158', 'comment' => 'Cabling WS'],
    ['name' => 'tci-bt-cab03', 'ip' => '10.100.104.197', 'comment' => 'Cabling WS'],
    ['name' => 'tci-bt-twexp', 'ip' => '10.100.104.172', 'comment' => 'Twinner workstation'],
    ['name' => 'tci-bt-qa02', 'ip' => '10.100.104.170', 'comment' => 'Cloud tester'],
];

$cablingSystems = [];

foreach ($cablingInventory as $system) {
    $resolvedIp = resolveHost($system['ip']);
    $ping = pingSystem($resolvedIp);

    $cablingSystems[] = [
        'name' => $system['name'],
        'ip' => $resolvedIp,
        'online' => $ping['online'],
        'latency' => $ping['latency'],
        'statusText' => $ping['online'] ? 'Online' : 'Offline',
	'comment'	=> $system['comment'] ?? ''
    ];
}

