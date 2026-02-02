<?php
require_once __DIR__ . '/windows_helpers.php';

$breakroomInventory = [
    ['name' => 'breakroom01', 'ip' => '10.100.104.164', 'comment' => 'TCI Video & Team pics'],
    ['name' => 'breakroom02', 'ip' => '10.100.104.226', 'comment' => 'OE Dashboard'],
    ['name' => 'breakroom03', 'ip' => '10.100.104.230', 'comment' => 'Floor monitor'],
    ['name' => 'tci-bt-testing', 'ip' => '10.100.104.171', 'comment' => 'New hire testing']
];

$breakroomSystems = [];

foreach ($breakroomInventory as $system) {
    $resolvedIp = resolveHost($system['ip']);
    $ping = pingSystem($resolvedIp);

    $breakroomSystems[] = [
        'name' => $system['name'],
        'ip' => $resolvedIp,
        'online' => $ping['online'],
        'latency' => $ping['latency'],
        'statusText' => $ping['online'] ? 'Online' : 'Offline',
	'comment'	=> $system['comment'] ?? ''
    ];
}

