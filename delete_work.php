<?php
require 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$workId = $_GET['work_id'];

// Hapus semua chapter terkait
$query = "DELETE FROM Chapters WHERE work_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $workId);
$stmt->execute();
$stmt->close();

// Hapus work
$query = "DELETE FROM Works WHERE id = ? AND author_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $workId, $userId);

if ($stmt->execute()) {
    header("Location: user_works.php");
    exit();
} else {
    echo "Error deleting work: " . $stmt->error;
}
$stmt->close();
?>
    