<?php
header('Content-Type: application/json');
include 'db_connect.php';

$records_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$data_type = isset($_GET['data']) ? $_GET['data'] : 'ph';
$offset = ($page - 1) * $records_per_page;

$total_result = $conn->query("SELECT COUNT(id) AS total FROM sensor_data");
$total_records = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

$column_value = 'ph_value';
if ($data_type === 'flow') $column_value = 'flow_rate';
if ($data_type === 'total') $column_value = 'total_volume';

// MODIFIKASI: Pastikan format tanggal di query SQL juga sama
$stmt = $conn->prepare("SELECT DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:%s') as time_label, $column_value AS value FROM sensor_data ORDER BY id DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $records_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    'total_pages' => $total_pages,
    'current_page' => $page,
    'data' => $data
]);

$stmt->close();
$conn->close();
?>