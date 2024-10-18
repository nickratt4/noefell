<?php
require '../config/database.php';
require'../includes/navbar_admin.php';


// Tambah kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category_name'])) {
    $categoryName = trim($_POST['category_name']);

    if (!empty($categoryName)) {
        $query = "INSERT INTO Categories (name) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $categoryName);

        if ($stmt->execute()) {
            $successMessage = "Category added successfully!";
        } else {
            $errorMessage = "Failed to add category.";
        }
        $stmt->close();
    }
}

// Hapus kategori
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $query = "DELETE FROM Categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $deleteId);

    if ($stmt->execute()) {
        $successMessage = "Category deleted successfully!";
    } else {
        $errorMessage = "Failed to delete category.";
    }
    $stmt->close();
}

// Ambil semua kategori
$query = "SELECT * FROM Categories";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Categories</h2>
        
        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <form action="manage_categories.php" method="POST">
            <div class="form-group">
                <label for="category_name">Category Name</label>
                <input type="text" class="form-control" id="category_name" name="category_name" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>

        <h3 class="mt-4">Existing Categories</h3>
        <ul class="list-group">
            <?php while ($category = $result->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo htmlspecialchars($category['name']); ?>
                    <a href="manage_categories.php?delete_id=<?php echo $category['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
