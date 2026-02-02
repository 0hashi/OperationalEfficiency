<?php
if (!extension_loaded('snmp')) {
    die('PHP SNMP extension is not installed or enabled.');
}


snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
snmp_set_quick_print(true);
// lib/snmp.php

// Global SNMP settings
snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);

/**
 * Get a single SNMP OID
 */
function snmp_get_value($ip, $community, $oid, $timeout = 1000000, $retries = 2)
{
    $result = @snmpget($ip, $community, $oid, $timeout, $retries);

    if ($result === false) {
        return null;
    }

    return trim($result);
}
?>
