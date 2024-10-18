<?php
require 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$chapterId = $_GET['chapter_id'];

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

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $content = $_POST['content'];

    $query = "UPDATE Chapters SET name = ?, content = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $name, $content, $chapterId);
    
    if ($stmt->execute()) {
        header("Location: view_work.php?work_id=" . $chapter['work_id']);
        exit();
    } else {
        echo "Error updating chapter: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Chapter - Novel Website</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-4">
        <h2>Edit Chapter</h2>
        <form action="edit_chapter.php?chapter_id=<?php echo $chapterId; ?>" method="post">
            <div class="form-group">
                <label for="name">Chapter Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($chapter['name']); ?>" required>
            </div>
            <div class="form-group mb-4">
                <label for="content">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($chapter['content']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Chapter</button>
        </form>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
