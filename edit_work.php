<?php
require 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$workId = $_GET['work_id'];

// Ambil detail work
$query = "SELECT * FROM Works WHERE id = ? AND author_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $workId, $userId);
$stmt->execute();
$work = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$work) {
    echo "Unauthorized access or Work not found.";
    exit();
}

// Ambil kategori untuk ditampilkan di dropdown
$query = "SELECT * FROM Categories";
$categoriesResult = $conn->query($query);

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $categoryId = $_POST['category_id']; // Ambil kategori dari form

    // Proses upload thumbnail
    $thumbnail = $work['thumbnail'];
    if ($_FILES['thumbnail']['name']) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES['thumbnail']['name']);
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFile);
        $thumbnail = basename($_FILES['thumbnail']['name']);
    }

    // Tambahkan category_id ke dalam query SQL
    $query = "UPDATE Works SET title = ?, description = ?, category_id = ?, thumbnail = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssisi", $title, $description, $categoryId, $thumbnail, $workId);

    if ($stmt->execute()) {
        header("Location: view_work.php?work_id=" . $workId);
        exit();
    } else {
        echo "Error updating work: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Work - Novel Website</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-4">
        <h2>Edit Work</h2>
        <form action="edit_works.php?work_id=<?php echo $workId; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($work['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select name="category_id" id="category" class="form-control" required>
                    <option value="">Select a category</option>
                    <?php while ($category = $categoriesResult->fetch_assoc()): ?>
                        <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $work['category_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($work['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="thumbnail">Thumbnail</label>
                <input type="file" class="form-control-file" id="thumbnail" name="thumbnail">
                <?php if ($work['thumbnail']): ?>
                <img src="uploads/<?php echo htmlspecialchars($work['thumbnail']); ?>" alt="Thumbnail" style="width:200px;height:200px;">
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Update Work</button>
        </form>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
