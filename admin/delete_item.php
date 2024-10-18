<?php
session_start();
require '../config/database.php';


// Ambil parameter dari URL
$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validasi jenis item yang ingin dihapus
if (!in_array($type, ['work', 'comment']) || $id <= 0) {
    echo "Invalid request.";
    exit;
}

try {
    // Mulai transaksi
    $conn->begin_transaction();

    // Hapus berdasarkan jenis laporan
    if ($type === 'work') {
        // Hapus komentar yang terkait dengan karya
        $commentQuery = "DELETE FROM Comments WHERE work_id = ?";
        $commentStmt = $conn->prepare($commentQuery);
        $commentStmt->bind_param("i", $id);
        $commentStmt->execute();

        // Hapus chapter yang terkait dengan karya
        $chapterQuery = "DELETE FROM Chapters WHERE work_id = ?";
        $chapterStmt = $conn->prepare($chapterQuery);
        $chapterStmt->bind_param("i", $id);
        $chapterStmt->execute();

        // Hapus karya itu sendiri
        $workQuery = "DELETE FROM Works WHERE id = ?";
        $workStmt = $conn->prepare($workQuery);
        $workStmt->bind_param("i", $id);
        $workStmt->execute();

    } elseif ($type === 'comment') {
        // Hapus komentar beserta balasannya
        $replyQuery = "DELETE FROM Comments WHERE parent_comment_id = ?";
        $replyStmt = $conn->prepare($replyQuery);
        $replyStmt->bind_param("i", $id);
        $replyStmt->execute();

        // Hapus komentar utama
        $query = "DELETE FROM Comments WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    $conn->commit();
    $_SESSION['success_message'] = "Item has been deleted successfully!";
    header('Location: manage_reports.php'); // Kembali ke halaman laporan
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo "Failed to delete item: " . $e->getMessage();
}
?>
