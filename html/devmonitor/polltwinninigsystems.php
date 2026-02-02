<?php
// Twinning systems
require_once __DIR__ . '/windows_helpers.php';

$twinningInventory = [
    ['name' => 'tci-bt-twexp', 'ip' => '10.100.104.172', 'comment' => 'Twinner'],
    ['name' => 'tci-bt-qa02', 'ip' => '10.100.104.170', 'comment' => 'Cloud tester'],
];

$twinningSystems = [];

foreach ($twinningInventory as $system) {
    $resolvedIp = resolveHost($system['ip']);
    $ping = pingSystem($resolvedIp);

    $twinningSystems[] = [
        'name' => $system['name'],
        'ip' => $resolvedIp,
        'online' => $ping['online'],
        'latency' => $ping['latency'],
        'statusText' => $ping['online'] ? 'Online' : 'Offline',
	'comment'    => $system['comment'] ?? ''
    ];
}

