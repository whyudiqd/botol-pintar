<?php
header('Content-Type: application/json');
include 'db_connect.php';

// Data apa yang diminta? (ph, flow, atau total)
$dataType = isset($_GET['data']) ? $_GET['data'] : 'ph';
$limit = 50; // Ambil 50 data terakhir

$sql = "";
$column_value = "";
$column_label = "timestamp";

switch ($dataType) {
    case 'flow':
        $column_value = 'flow_rate';
        break;
    case 'total':
        $column_value = 'total_volume';
        break;
    default:
        $column_value = 'ph_value';
}

$sql = "SELECT $column_value, DATE_FORMAT($column_label, '%H:%i:%s') as time_label FROM sensor_data ORDER BY id DESC LIMIT $limit";

$result = $conn->query($sql);

$labels = [];
$values = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $labels[] = $row['time_label'];
        $values[] = $row[$column_value];
    }
}

// Data dibalik agar urutan waktu dari kiri ke kanan (lama ke baru)
echo json_encode(['labels' => array_reverse($labels), 'values' => array_reverse($values)]);

$conn->close();
?>