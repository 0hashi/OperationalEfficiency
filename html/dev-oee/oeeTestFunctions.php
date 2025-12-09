<?php
// =============================================================================

function progresBar($conn) {
    header('Content-Type: application/json');

    // Example: calculate % complete from your table
    $sql = "SELECT (SUM(completed_quantity) / SUM(total_quantity)) * 100 AS progress FROM my_table";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $progress = round($row['progress'], 2); // 2 decimal places
    } else {
        $progress = 0;
    }

    $conn->close();

    echo json_encode(["progress" => $progress]);
}
/*
function indirectLaborWorkCentersDailyDev($conn) {
    $sql = "
    SELECT
        actual_work_center,
        employee_name,
        REGEXP_REPLACE(pwo_number, '^[A-Z]+ [0-9]{4} ', '') AS pwo_number,
        DATE_FORMAT(start_date, '%H:%i') AS start_time,
        DATE_FORMAT(stop_date, '%H:%i')  AS stop_time,
        total_hours
    FROM
        v_pwo_labor
    WHERE
        start_date >= CURDATE()
        AND work_center = 'ZIND'
    ORDER BY actual_work_center
    ";

    $result = $conn->query($sql);

    if (!$result) {
        return "<tr><td colspan='2'>Error: " . htmlspecialchars($conn->error) . "</td></tr>";
    }

    if ($result->num_rows === 0) {
        return "<tr><td colspan='2'>No data found.</td></tr>";
    }

    $rows = '<tr><td colspan=6 style="background: lightgrey; font-weight: bold; text-align: center;">
        <a href="indirectlaborworkcenter.php" style="text-decoration: none; color: #000;">'
        . date('l m/d') . '</a></td></tr>
        <tr>
            <td style="background: lightgrey; font-weight: bold; text-align: center;">Work Center</td>
            <td style="background: lightgrey; font-weight: bold; text-align: center;">Operator</td>
            <td style="background: lightgrey; font-weight: bold; text-align: center;">PWO</td>
            <td style="background: lightgrey; font-weight: bold; text-align: center;">Start</td>
            <td style="background: lightgrey; font-weight: bold; text-align: center;">Stop</td>
            <td style="background: lightgrey; font-weight: bold; text-align: center;">Time</td>
        </tr>';

    $bgcount = 0;

    while ($row = $result->fetch_assoc()) {
        $backgroundColor = ($bgcount % 2 == 0) ? 'background-color: #ddecf0' : '';
        $bgcount++;

        $rows .= "<tr style=\"{$backgroundColor}\">
            <td>" . htmlspecialchars(substr($row['actual_work_center'], 0, 11)) . "</td>
            <td>{$row['employee_name']}</td>
            <td>{$row['pwo_number']}</td>
            <td>{$row['start_time']}</td>
            <td>{$row['stop_time']}</td>
            <td>{$row['total_hours']}</td>
        </tr>";
    }

    return $rows;
}
*/



?>