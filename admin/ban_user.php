<?php
session_start();
require '../config/database.php';




// Proses ban saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['user_id']);
    $banDuration = intval($_POST['ban_duration']);
    $banReason = trim($_POST['ban_reason']);

    // Hitung tanggal hingga kapan user di-ban
    $banUntil = date('Y-m-d H:i:s', strtotime("+$banDuration days"));

    // Masukkan data ban ke dalam tabel bans
    $query = "INSERT INTO bans (banned_user_id, banned_until, reason, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $userId, $banUntil, $banReason);

    if ($stmt->execute()) {
        echo "User has been banned successfully!";
        header('Location: manage_reports.php'); // Kembali ke halaman manage reports
        exit;
    } else {
        echo "Failed to ban user.";
    }

    $stmt->close();
}
?>
