// Functions to convert numeric codes
function hrPrinterStatus($code) {
    return match ((int)$code) {
        1 => 'Other/Unknown',
        2 => 'Unknown',
        3 => 'Idle',
        4 => 'Printing',
        5 => 'Warmup',
        default => 'Error'
    };
}

// Decode hrPrinterDetectedErrorState bitmask
function decodePrinterErrors($mask) {
    $mask = (int)$mask;
    $errors = [];
    if ($mask & 0b00000100) $errors[] = 'No Paper';
    if ($mask & 0b00001000) $errors[] = 'Low Ribbon';
    if ($mask & 0b00010000) $errors[] = 'Door Open';
    if ($mask & 0b00100000) $errors[] = 'Jam';
    if ($mask & 0b01000000) $errors[] = 'Offline';
    return empty($errors) ? ['OK'] : $errors;
}

function hrBinary($code) {
    return ((int)$code === 0) ? 'OK' : 'Alert';
}

function hrPaused($code) {
    return ((int)$code === 0) ? 'Not Paused' : 'Paused';
}

function decodePrinterErrors($mask) {
    $mask = (int)$mask;
    $errors = [];
    if ($mask & 0b00000100) $errors[] = 'No Paper';
    if ($mask & 0b00001000) $errors[] = 'Low Ribbon';
    if ($mask & 0b00010000) $errors[] = 'Door Open';
    if ($mask & 0b00100000) $errors[] = 'Jam';
    if ($mask & 0b01000000) $errors[] = 'Offline';
    return empty($errors) ? ['OK'] : $errors;
}

/**
 * Get the subnet mask for a given IP
 */
function getSubnetMask($printerIP, $community) {
    $maskTable = snmp2_walk($printerIP, $community, "1.3.6.1.2.1.4.20.1.3");
    if ($maskTable === false) return null;

    foreach ($maskTable as $entry) {
        // Entry format: STRING: "255.255.255.0"
        if (strpos($entry, $printerIP) !== false || preg_match('/\d+\.\d+\.\d+\.\d+/', $entry)) {
            return trim(str_replace(['STRING:','"'], '', $entry));
        }
    }
    return null;
}

/**
 * Get the default gateway
 */
function getDefaultGateway($printerIP, $community) {
    $routeTable = snmp2_walk($printerIP, $community, "1.3.6.1.2.1.4.21.1.7"); // ipRouteNextHop
    if ($routeTable === false) return null;

    foreach ($routeTable as $key => $value) {
        // The default route row has index 0.0.0.0
        if (strpos($key, '0.0.0.0') !== false) {
            return trim(str_replace(['IpAddress:','STRING:','"'], '', $value));
        }
    }
    return null;
}

