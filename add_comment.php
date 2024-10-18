<?php
session_start();
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chapterId = isset($_POST['chapter_id']) ? intval($_POST['chapter_id']) : 0;
    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $parentCommentId = isset($_POST['parent_comment_id']) ? intval($_POST['parent_comment_id']) : null;

    if ($chapterId && $userId && $content) {
        // Dapatkan work_id dari chapter_id
        $query = "SELECT work_id FROM Chapters WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $chapterId);
        $stmt->execute();
        $chapter = $stmt->get_result()->fetch_assoc();
        $workId = $chapter['work_id'];
        $stmt->close();

        if ($parentCommentId) {
            // Komentar balasan
            $query = "INSERT INTO Comments (chapter_id, user_id, content, parent_comment_id, work_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iisis", $chapterId, $userId, $content, $parentCommentId, $workId);
        } else {
            // Komentar baru
            $query = "INSERT INTO Comments (chapter_id, user_id, content, work_id, created_at) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iiss", $chapterId, $userId, $content, $workId);
        }

        if ($stmt->execute()) {
            $newCommentId = $stmt->insert_id; // Dapatkan ID komentar yang baru ditambahkan
            header("Location: view_chapter.php?chapter_id=" . $chapterId . "&new_comment_id=" . $newCommentId);
            exit;
        } else {
            echo "Failed to add comment.";
        }
        $stmt->close();
    } else {
        echo "All fields are required.";
    }
} else {
    echo "Invalid request method.";
}
?>
