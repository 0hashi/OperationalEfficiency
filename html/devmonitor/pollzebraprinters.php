<?php
# pollzebraprinters.php
require_once __DIR__ . '/lib/snmp.php';

// SNMP performance tuning
if (function_exists('snmp_set_quick_print')) {
    snmp_set_quick_print(true);
}

if (defined('SNMP_VALUE_PLAIN') && function_exists('snmp_set_valueretrieval')) {
    snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
}

if (function_exists('snmp_set_timeout')) {
    snmp_set_timeout(1000000); // 1 second
}

if (function_exists('snmp_set_retries')) {
    snmp_set_retries(1);
}


$community = "public";

$printerInventory = [
    ['name' => 'E1/E3',      'ip' => '10.100.104.48', 'comment' => 'E1/E3 - T3N232400936'],
    ['name' => 'Coiling',    'ip' => '10.100.104.49', 'comment' => 'Coiling - T3N232400358'],
    ['name' => 'E2',         'ip' => '10.100.104.50', 'comment' => 'E2 - T3J240300724, AllState Warranty in printer'],
    ['name' => 'E4/E5',      'ip' => '10.100.104.51', 'comment' => 'E4/E5 - T3N233500050'],
    ['name' => 'TW/CBL',     'ip' => '10.100.104.52', 'comment' => 'TW/CBL - T3N232400951'],
    ['name' => 'Receiving', 'ip' => '10.100.104.53', 'comment' => 'Receiving - T3N232400344'],
    ['name' => 'Spooler',    'ip' => '10.100.104.54', 'comment' => 'Spooler - T3N233500066'],
    ['name' => 'Braiding',   'ip' => '10.100.104.56', 'comment' => 'Braiding - T3N232400943'],
    ['name' => 'E6',         'ip' => '10.100.104.57', 'comment' => 'E6 - T3J240300730, AllState Warranty in printer'],
    ['name' => 'IT',         'ip' => '10.100.104.58', 'comment' => 'IT - T3J231105910'],
];


/* =========================
   OIDs
   ========================= */
$oids = [
    'sysDescr'      => '1.3.6.1.2.1.1.1.0',
    'sysName'       => '1.3.6.1.2.1.1.5.0',
    'printerStatus' => '1.3.6.1.2.1.25.3.5.1.1.1',
    'errorMask'     => '1.3.6.1.2.1.25.3.5.1.2.1',
];

/* =========================
   Helper Functions
   ========================= */
function isHostOnline($ip) {
    exec("ping -c 1 -W 1 $ip 2>/dev/null", $out, $status);
    return $status === 0;
}

function hrPrinterStatus($code) {
    return match ((int)$code) {
        1 => 'Other',
        2 => 'Unknown',
        3 => 'Idle',
        4 => 'Printing',
        5 => 'Warmup',
        default => 'Error'
    };
}

function snmpSafeGet($ip, $community, $oid) {
    $value = @snmpget($ip, $community, $oid);
    if ($value === false || $value === null) {
        return false;
    }

    // Remove SNMP type prefixes (STRING:, INTEGER:, etc.)
    $value = preg_replace('/^[A-Z\-]+:\s*/', '', $value);

    // Remove surrounding quotes
    $value = trim($value, "\" \t\n\r\0\x0B");

    // Normalize Zebra-style pipes (turn " | " into " - ")
    $value = preg_replace('/\s*\|\s*/', ' - ', $value);

    return $value;
}

function decodePrinterErrors($errorMask) {
    if (!is_numeric($errorMask)) return [];
    $mask = (int)$errorMask;
    $errors = [];
    if ($mask & 1)   $errors[] = 'Other';
    if ($mask & 2)   $errors[] = 'No paper';
    if ($mask & 4)   $errors[] = 'Low paper';
    if ($mask & 8)   $errors[] = 'Low toner';
    if ($mask & 16)  $errors[] = 'Door open';
    if ($mask & 32)  $errors[] = 'Jammed';
    if ($mask & 64)  $errors[] = 'Offline';
    if ($mask & 128) $errors[] = 'Service requested';
    return $errors;
}

function getSubnetMask($ip, $community) {
    $oid = "1.3.6.1.2.1.4.20.1.3.$ip";
    $val = @snmpget($ip, $community, $oid);
    return $val !== false ? trim($val) : 'N/A';
}

function getDefaultGateway($ip, $community) {
    $oid = "1.3.6.1.2.1.4.21.1.7.0.0.0.0";
    $val = @snmpget($ip, $community, $oid);
    return $val !== false ? trim($val) : 'N/A';
}

/* =========================
   Build Final Printer Array
   ========================= */
$printers = [];

foreach ($printerInventory as $p) {
    $ip = $p['ip'];

    // 🚀 Fast pre-check
    $online = isHostOnline($ip);

    if (!$online) {
        $printers[] = [
            'ip'         => $ip,
            'sysName'    => $p['name'],
            'sysDescr'   => 'N/A',
            'statusText' => 'Offline',
            'errors'     => [],
            'subnet'     => 'N/A',
            'gateway'    => 'N/A',
            'offline'    => true,
            'comment'    => $p['comment'] ?? ''
        ];
        continue;
    }

    // SNMP only runs if host is reachable
    $sysNameRaw   = snmpSafeGet($ip, $community, $oids['sysName']);
    $sysDescr     = snmpSafeGet($ip, $community, $oids['sysDescr']);
    $statusRaw    = snmpSafeGet($ip, $community, $oids['printerStatus']);
    $errorMaskRaw = snmpSafeGet($ip, $community, $oids['errorMask']);

    $statusText = hrPrinterStatus((int)$statusRaw);

    $errors = is_numeric($errorMaskRaw)
        ? decodePrinterErrors((int)$errorMaskRaw)
        : [];

    $printers[] = [
        'ip'         => $ip,
        'sysName'    => $sysNameRaw ?: $p['name'],
        'sysDescr'   => $sysDescr ?: 'N/A',
        'statusText' => $statusText,
        'errors'     => $errors,
        'subnet'     => getSubnetMask($ip, $community),
        'gateway'    => getDefaultGateway($ip, $community),
        'offline'    => false,
        'comment'    => $p['comment'] ?? ''
    ];
}

