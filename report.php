<?php
session_start();
require 'config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

$type = $data['type']; // work or comment
$reportedId = intval($data['reportedId']);
$reportedUserId = intval($data['reportedUserId']);
$reporterId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$reason = trim($data['reason']);

if ($reporterId && $reason) {
    $query = "INSERT INTO reports (report_type, reported_id, reported_user_id, reporter_id, reason) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siiss", $type, $reportedId, $reportedUserId, $reporterId, $reason);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Report submitted successfully.']);
    } else {
        echo json_encode(['message' => 'Failed to submit report.']);
    }
    $stmt->close();
} else {
    echo json_encode(['message' => 'Invalid request.']);
}
?>
