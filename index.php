<?php
require 'config/database.php';
session_start();

// Inisialisasi variabel $order untuk mengatur cara pengurutan
$order = isset($_GET['order']) ? $_GET['order'] : 'terbaru';

// Query dasar untuk mengambil semua works dengan jumlah chapter dan rata-rata rating
$query = "SELECT w.*, COUNT(c.id) as total_chapters, AVG(r.rating) as average_rating 
          FROM Works w
          LEFT JOIN Chapters c ON w.id = c.work_id
          LEFT JOIN Ratings r ON w.id = r.work_id 
          GROUP BY w.id";

// Tambahkan pengurutan berdasarkan pilihan
switch ($order) {
    case 'populer':
        $query .= " ORDER BY average_rating DESC";
        break;
    case 'terbaru':
        $query .= " ORDER BY w.created_at DESC";
        break;
    case 'acak':
        $query .= " ORDER BY RAND()";
        break;
}

// Eksekusi query
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
    <?php include 'includes/logout_modal.php'; ?>

    <div class="container mt-4">
        <!-- Tombol untuk mengurutkan karya -->
        <div class="mb-3">
            <a href="index.php?order=populer" class="btn btn-primary">Paling Populer</a>
            <a href="index.php?order=terbaru" class="btn btn-secondary">Terbaru</a>
            <a href="index.php?order=acak" class="btn btn-success">Acak</a>
        </div>
        
        <div class="row">
            <?php if ($works->num_rows > 0): ?>
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
            <?php else: ?>
                <div class="col-md-12">
                    <div class="alert alert-warning" role="alert">
                        Tidak ada karya yang ditemukan.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
<script>
    // Mencegah klik kanan
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    // Mencegah kombinasi tombol untuk membuka DevTools
    document.onkeydown = function(e) {
        if (e.keyCode == 123 || // F12
            (e.ctrlKey && e.shiftKey && e.keyCode == 73) || // Ctrl+Shift+I
            (e.ctrlKey && e.shiftKey && e.keyCode == 74) || // Ctrl+Shift+J
            (e.ctrlKey && e.keyCode == 85) || // Ctrl+U (Lihat sumber)
            (e.ctrlKey && e.keyCode == 83)) { // Ctrl+S
            e.preventDefault();
            return false;
        }
    };
</script>
