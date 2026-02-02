<?php

function resolveHost($value) {
    if (filter_var($value, FILTER_VALIDATE_IP)) {
        return $value;
    }

    $resolved = gethostbyname($value);
    return ($resolved === $value) ? 'Unresolved' : $resolved;
}

function pingSystem($ip) {
    if ($ip === 'Unresolved') {
        return ['online' => false, 'latency' => null];
    }

    exec("ping -c 1 -W 1 " . escapeshellarg($ip), $output, $status);

    if ($status !== 0) {
        return ['online' => false, 'latency' => null];
    }

    foreach ($output as $line) {
        if (preg_match('/time=([\d\.]+)\s*ms/', $line, $matches)) {
            return ['online' => true, 'latency' => $matches[1]];
        }
    }

    return ['online' => true, 'latency' => null];
}

