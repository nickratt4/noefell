<?php
require 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil kategori untuk ditampilkan di dropdown
$query = "SELECT * FROM Categories";
$categoriesResult = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description']; 
    $categoryId = $_POST['category_id']; // Ambil kategori dari form
    $authorId = $_SESSION['user_id'];
    $thumbnail = $_FILES['thumbnail']['name'];
    $thumbnailTemp = $_FILES['thumbnail']['tmp_name'];

    if ($thumbnail) {
        move_uploaded_file($thumbnailTemp, "uploads/" . $thumbnail);
    }

    // Tambahkan category_id ke dalam query SQL
    $query = "INSERT INTO works (title, description, category_id, author_id, thumbnail) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssiss", $title, $description, $categoryId, $authorId, $thumbnail);

    if ($stmt->execute()) {
        header("Location: user_works.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Work - Novel Website</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-4">
        <h2>Add New Work</h2>
        <form action="add_work.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select name="category_id" id="category" class="form-control" required>
                    <option value="">Select a category</option>
                    <?php while ($category = $categoriesResult->fetch_assoc()): ?>
                        <option value="<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="thumbnail">Thumbnail</label>
                <input type="file" class="form-control" id="thumbnail" name="thumbnail">
            </div>

            <button type="submit" class="btn btn-primary">Add Work</button>
        </form>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
