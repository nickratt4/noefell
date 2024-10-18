<?php
require 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$query = "SELECT * FROM Works WHERE author_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$works = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Works - Novel Website</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-4">
        <h2>My Works</h2>
        <a href="add_work.php" class="btn btn-primary mb-3">Add New Work</a>
        <div class="row">
            <?php while ($work = $works->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="uploads/<?php echo htmlspecialchars($work['thumbnail']); ?>" class="card-img-top" alt="Thumbnail">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($work['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(substr($work['description'], 0, 100)) . '...'; ?></p>
                        <a href="view_work.php?work_id=<?php echo $work['id']; ?>" class="btn btn-primary">View Work</a>
                        <a href="edit_work.php?work_id=<?php echo $work['id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="delete_work.php?work_id=<?php echo $work['id']; ?>" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
