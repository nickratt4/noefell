<?php
require 'config/database.php';




session_start();

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi pencarian
function search($keyword) {
    global $conn;
    $keyword = $conn->real_escape_string($keyword);
    $sql = "SELECT w.*, COUNT(c.id) as chapter_count
            FROM Works w
            LEFT JOIN Chapters c ON w.id = c.work_id
            WHERE w.title LIKE '%$keyword%' OR w.description LIKE '%$keyword%'
            GROUP BY w.id";
    $result = $conn->query($sql);
    return $result;
}

// Proses pencarian
if (isset($_GET['search'])) {
    $keyword = $_GET['search'];
    $searchResult = search($keyword);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>noefell</title>
    <link rel="stylesheet" href="includes/search.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <?php include 'includes/cari.php'; ?>
<body>
    <div class="container mt-4">
        <h2>Hasil Pencarian</h2>



        <!-- Search Bar -->
        <form class="search-bar" method="GET" action="">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="noefell" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" required>
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </div>
        </form>

        <?php
        if (isset($searchResult) && $searchResult->num_rows > 0) {
            echo "<div class='search-results'>";
            while($row = $searchResult->fetch_assoc()) {
                // Ubah link href ke halaman view_work.php
                echo "<a href='view_work.php?work_id=" . htmlspecialchars($row['id']) . "' class='search-item'>";
                echo "<img src='uploads/" . htmlspecialchars($row['thumbnail']) . "' alt='Thumbnail'>";
                echo "<div class='search-info'>";
                echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                echo "<p><strong>Jumlah Chapter: </strong>" . htmlspecialchars($row['chapter_count']) . "</p>";
                echo "</div>";
                echo "</a>";
            }
            echo "</div>";
        } elseif (isset($searchResult)) {
            echo "<p>Tidak ada hasil yang ditemukan.</p>";
        }
        ?>

    </div>
</body>
</html>
