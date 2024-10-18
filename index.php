<?php
require 'config/database.php';
session_start();

// Ambil semua works dengan total rating
$query = "SELECT w.*, COUNT(c.id) as total_chapters, 
          AVG(r.rating) as average_rating 
          FROM Works w
          LEFT JOIN Chapters c ON w.id = c.work_id
          LEFT JOIN Ratings r ON w.id = r.work_id
          GROUP BY w.id";
$stmt = $conn->prepare($query);
$stmt->execute();
$works = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index - Novel Website</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="includes/index.css">
    </head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-4">
        <div class="row">
            <?php while ($work = $works->fetch_assoc()): ?>
            <div class="col-md-6 work-card"> 
                <a href="view_work.php?work_id=<?php echo $work['id']; ?>" class="card d-flex flex-row align-items-center">
                    <img src="uploads/<?php echo htmlspecialchars($work['thumbnail']); ?>" class="card-img-left" alt="Thumbnail">
                    <div class="card-body d-flex flex-column work-info">
                        <h5 class="work-card-title"><?php echo htmlspecialchars($work['title']); ?></h5>
                        <p class="chapter-info">Chapter <?php echo $work['total_chapters']; ?></p>
                        <div class="rating">
                            <span class="star">&#9733;</span>
                            <span class="average-rating"><?php echo number_format($work['average_rating'], 1); ?> / 5</span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
