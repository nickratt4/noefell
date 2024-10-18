<?php
session_start();
require 'config/database.php';

// Cek apakah pengguna login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$workId = isset($_GET['work_id']) ? intval($_GET['work_id']) : 0;
$isLoggedIn = isset($_SESSION['user_id']);
$loggedInUserId = $isLoggedIn ? $_SESSION['user_id'] : 0;

// Cek apakah pengguna login adalah penulis karya
$query = "SELECT author_id FROM Works WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $workId);
$stmt->execute();
$work = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($work['author_id'] != $loggedInUserId) {
    echo "You are not authorized to add chapters to this work.";
    exit;
}

// Proses form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $content = $_POST['content'];

    $query = "INSERT INTO Chapters (name, content, work_id, user_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $name, $content, $workId, $loggedInUserId);
    
    if ($stmt->execute()) {
        echo "Chapter added successfully.";
        header("Location: view_work.php?work_id=" . $workId);
        exit;
    } else {
        echo "Error adding chapter.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Add Chapter</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-5">
        <h1>Add Chapter to Work</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="name">Chapter Title</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="content">Chapter Content</label>
                <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Chapter</button>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
