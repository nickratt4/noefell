<?php
session_start();
require 'config/database.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Cek apakah ada chapter yang sedang dibaca (penyimpanan riwayat baca otomatis)
if (isset($_GET['chapter_id']) && isset($_GET['work_id'])) {
    $chapterId = $_GET['chapter_id'];
    $workId = $_GET['work_id'];

    // Cek apakah sudah ada dalam riwayat baca
    $checkQuery = "SELECT * FROM readinghistory WHERE user_id = ? AND chapter_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $userId, $chapterId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows == 0) {
        // Simpan riwayat baca baru
        $insertQuery = "INSERT INTO readinghistory (user_id, chapter_id, read_at) VALUES (?, ?, NOW())";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ii", $userId, $chapterId);
        $insertStmt->execute();
    } else {
        // Perbarui waktu baca terakhir jika sudah ada
        $updateQuery = "UPDATE readinghistory SET read_at = NOW() WHERE user_id = ? AND chapter_id = ?";
        $updateStmt->bind_param("ii", $userId, $chapterId);
        $updateStmt->execute();
    }

    // Arahkan pengguna ke halaman chapter
    header("Location: read.php?chapter_id=$chapterId&work_id=$workId");
    exit;
}

// Query untuk mengambil riwayat baca pengguna
$query = "
    SELECT w.title, c.name as chapter_name, w.thumbnail, rh.read_at, c.id as chapter_id, w.id as work_id
    FROM readinghistory rh
    JOIN chapters c ON rh.chapter_id = c.id
    JOIN works w ON c.work_id = w.id
    WHERE rh.user_id = ?
    ORDER BY rh.read_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>History</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 15px;
            width: 30%;
            display: inline-block;
            vertical-align: top;
            margin-right: 1.5%;
        }
        .card img {
            width: 100%;
            border-radius: 5px;
        }
        .card h2 {
            font-size: 18px;
            margin: 10px 0;
        }
        .card p {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }
        .progress-bar {
            background-color: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-bar div {
            height: 5px;
            background-color: #007bff;
        }
        .progress-text {
            font-size: 14px;
            color: #555;
            background-color: #f5f5f5;
            padding: 5px;
            border-radius: 5px;
            text-align: center;
        }
        @media (max-width: 768px) {
            .card {
                width: 45%;
                margin-right: 5%;
            }
        }
        @media (max-width: 480px) {
            .card {
                width: 100%;
                margin-right: 0;
            }
        }
    </style>
</head>
<body>

<?php include 'includes/nav-his.php'; ?>

<div class="container">
    <h1>History</h1>
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img class="card-img-top" src="uploads/<?php echo htmlspecialchars($row['thumbnail']); ?>" alt="Work Thumbnail">
                    <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                    <p>Chapter <?php echo htmlspecialchars($row['chapter_name']); ?></p>

                    <a href="view_work.php?chapter_id=<?php echo $row['chapter_id']; ?>&work_id=<?php echo $row['work_id']; ?>" class="btn btn-primary">Lanjutkan Membaca</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-md-12">
                <div class="alert alert-warning" role="alert">
                    Belum ada riwayat baca.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>

