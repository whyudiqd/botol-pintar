<?php
include 'db_connect.php';

// Hanya proses jika metode request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dan validasi
    $ph = isset($_POST['ph']) ? floatval($_POST['ph']) : null;
    $flow = isset($_POST['flow']) ? floatval($_POST['flow']) : null;
    $total = isset($_POST['total']) ? floatval($_POST['total']) : null;

    if ($ph !== null && $flow !== null && $total !== null) {
        // Gunakan prepared statements untuk keamanan
        $stmt = $conn->prepare("INSERT INTO sensor_data (ph_value, flow_rate, total_volume) VALUES (?, ?, ?)");
        $stmt->bind_param("ddd", $ph, $flow, $total);

        if ($stmt->execute()) {
            echo "Data berhasil disimpan.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Data tidak lengkap.";
    }
} else {
    echo "Metode request tidak valid.";
}

$conn->close();
?>