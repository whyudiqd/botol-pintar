<?php
// Atur header untuk koneksi Server-Sent Events
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

include 'db_connect.php';

// Variabel untuk menyimpan ID data terakhir yang dikirim
$lastId = 0;

// Loop tanpa henti untuk terus memeriksa data baru
while (true) {
    // MODIFIKASI: Mengubah format tanggal di query SQL
    $stmt = $conn->prepare("SELECT id, ph_value, flow_rate, total_volume, DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:%s') as time_label FROM sensor_data ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    $latestData = $result->fetch_assoc();

    if ($latestData && $latestData['id'] > $lastId) {
        // Kirim data ke klien dalam format SSE
        echo "data: " . json_encode($latestData) . "\n\n";

        ob_flush();
        flush();

        // Perbarui ID terakhir yang dikirim
        $lastId = $latestData['id'];
    }

    if (connection_aborted()) {
        break;
    }

    // Beri jeda 1 detik untuk mengurangi beban CPU server
    sleep(1);
}

$stmt->close();
$conn->close();
?>