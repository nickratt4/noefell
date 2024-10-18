<?php
require 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$chapterId = $_GET['chapter_id'];
$workId = $_GET['work_id'];

// Ambil detail chapter
$query = "SELECT * FROM Chapters WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $chapterId);
$stmt->execute();
$chapter = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$chapter) {
    echo "Chapter not found.";
    exit();
}

// Ambil detail work dan pastikan pengguna adalah pemiliknya
$query = "SELECT * FROM Works WHERE id = ? AND author_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $chapter['work_id'], $userId);
$stmt->execute();
$work = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$work) {
    echo "Unauthorized access or Work not found.";
    exit();
}

// Hapus chapter
$query = "DELETE FROM Chapters WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $chapterId);

if ($stmt->execute()) {
    header("Location: view_work.php?work_id=" . $workId);
    exit();
} else {
    echo "Error deleting chapter: " . $stmt->error;
}
$stmt->close();
?>
