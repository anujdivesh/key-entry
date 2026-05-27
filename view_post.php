<?php

require_once("app_code/session.php");
require_once("app_code/class.user.php");
require_once("app_code/class.division.php");
$auth_user = new USER();

$station=$_POST['data'];
$date=$_POST['date'];

$data = $auth_user->search_obsdata($station, $date);

if (!is_array($data) || count($data) === 0) {
    echo "";
    exit;
}

function esc_html($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Build column order based on sensors table, so headers match SELECT sensor_name FROM sensors.
$sensor_stmt = $auth_user->runQuery("SELECT id, sensor_name, reference_col FROM sensors ORDER BY id");
$sensor_stmt->execute();
$sensors = $sensor_stmt->fetchAll(PDO::FETCH_ASSOC);

$sensor_cols = [];
$sensor_label_by_ref = [];
foreach ($sensors as $sensor) {
    $ref = isset($sensor['reference_col']) ? trim((string)$sensor['reference_col']) : '';
    if ($ref === '') {
        continue;
    }
    // Safety: allow only simple SQL identifiers.
    if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $ref)) {
        continue;
    }
    $sensor_cols[] = $ref;
    $sensor_label_by_ref[$ref] = isset($sensor['sensor_name']) ? (string)$sensor['sensor_name'] : $ref;
}

$first_row = $data[0];
$all_keys = array_keys($first_row);

// Prefer these metadata columns first, then all sensors, then any remaining obs_data fields.
$preferred_keys = ['id', 'station_label', 'date_entry_fmt'];

$keys = [];
foreach ($preferred_keys as $key) {
    if (in_array($key, $all_keys, true)) {
        $keys[] = $key;
    }
}
foreach ($sensor_cols as $ref) {
    if (in_array($ref, $all_keys, true) && !in_array($ref, $keys, true)) {
        $keys[] = $ref;
    }
}
foreach ($all_keys as $key) {
    if (!in_array($key, $keys, true)) {
        $keys[] = $key;
    }
}

$label_map = [
    'id' => 'ID',
    'station_label' => 'Station',
    'date_entry_fmt' => 'Date Entry',
];


$has_remarks = in_array('remarks', $keys, true);
if ($has_remarks) {
    $remarks_idx = array_search('remarks', $keys, true);
    if ($remarks_idx !== false) {
        unset($keys[$remarks_idx]);
    }
    $keys = array_values($keys);
}


$table = "<thead class='thead-default'><tr>";
$table .= "<th>Edit</th>";
foreach ($keys as $key) {
    if (isset($sensor_label_by_ref[$key])) {
        $label = $sensor_label_by_ref[$key];
    } else {
        $label = isset($label_map[$key]) ? $label_map[$key] : ucwords(str_replace('_', ' ', (string)$key));
    }
    $table .= "<th>" . esc_html($label) . "</th>";
}
if ($has_remarks) {
    $table .= "<th>Remarks</th>";
}
$table .= "</tr></thead>";

$table .= "<tbody>";
foreach($data as $row){
    $table .= "<tr>";
    $id = isset($row['id']) ? $row['id'] : '';
    $table .= "<td><a href='read_data.php?data_id=".urlencode(base64_encode($id))."' class= 'btn btn-warning btn-xs m-r-5' data-toggle='tooltip' data-original-title='Edit'><i class='fa fa-pencil font-14'></i></a></td>";
    foreach ($keys as $key) {
        $value = isset($row[$key]) ? $row[$key] : '';
        if ($value === null) {
            $value = '';
        }
        $table .= "<td>" . esc_html($value) . "</td>";
    }
    if ($has_remarks) {
        $table .= "<td>" . esc_html(isset($row['remarks']) ? $row['remarks'] : '') . "</td>";
    }
    $table .= "</tr>";
}
$table .= "</tbody>";

echo $table;

?>