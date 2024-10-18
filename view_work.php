<?php
session_start();
require 'config/database.php';

// Ambil ID karya dari URL
$workId = isset($_GET['work_id']) ? intval($_GET['work_id']) : 0;

// Ambil informasi karya
$query = "SELECT * FROM Works WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $workId);
$stmt->execute();
$workResult = $stmt->get_result();
$work = $workResult->fetch_assoc();
$stmt->close();

if (!$work) {
    echo "Work not found!";
    exit;
}

// Periksa apakah pengguna login
$isLoggedIn = isset($_SESSION['user_id']);
$loggedInUserId = $isLoggedIn ? $_SESSION['user_id'] : 0;

// Cek apakah pengguna login adalah penulis karya
$isAuthor = $work['author_id'] == $loggedInUserId;

// Cek apakah pengguna sudah memberikan rating sebelumnya
$existingRating = null;
if ($isLoggedIn) {
    $query = "SELECT rating FROM ratings WHERE work_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $workId, $loggedInUserId);
    $stmt->execute();
    $existingRating = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Pesan notifikasi untuk ditampilkan
$notificationMessage = '';

// Jika form rating dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isLoggedIn) {
    $rating = intval($_POST['rating']);

    if ($existingRating) {
        // Update rating yang sudah ada
        $query = "UPDATE ratings SET rating = ? WHERE work_id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $rating, $workId, $loggedInUserId);
    } else {
        // Insert rating baru
        $query = "INSERT INTO ratings (work_id, user_id, rating) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $workId, $loggedInUserId, $rating);
    }

    if ($stmt->execute()) {
        $notificationMessage = "Rating submitted!";
    } else {
        $notificationMessage = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Menghitung rata-rata rating karya
$query = "SELECT AVG(rating) AS average_rating FROM ratings WHERE work_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $workId);
$stmt->execute();
$avgRatingResult = $stmt->get_result()->fetch_assoc();
$averageRating = round($avgRatingResult['average_rating'], 2);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>View Work</title>
    <style>
        /* Style untuk bintang rating */
        .stars {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .stars input {
            display: none;
        }

        .stars label {
            font-size: 2em;
            color: gray;
            cursor: pointer;
            transition: color 0.2s;
        }

        .stars input:checked ~ label,
        .stars label:hover,
        .stars label:hover ~ label {
            color: gold;
        }

        #notification {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #28a745;
            color: white;
            padding: 20px;
            border-radius: 10px;
            z-index: 1000;
            font-size: 18px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Pop-up Notification -->
    <div id="notification"><?php echo $notificationMessage; ?></div>

    <div class="container mt-5">
        <h1><?php echo htmlspecialchars($work['title']); ?></h1>
        <img src="uploads/<?php echo htmlspecialchars($work['thumbnail']); ?>" alt="Thumbnail" style="width: 200px;">
        <p><?php echo htmlspecialchars($work['description']); ?></p>

        <!-- Rata-rata Rating -->
        <p>Average Rating: <?php echo $averageRating; ?> / 5</p>

        <!-- Form untuk memberikan rating dengan bintang jika login -->
        <?php if ($isLoggedIn): ?>
            <form id="ratingForm" action="view_work.php?work_id=<?php echo $work['id']; ?>" method="POST">
                <div class="stars">
                    <input type="radio" id="star5" name="rating" value="5" <?php if ($existingRating && $existingRating['rating'] == 5) echo 'checked'; ?>>
                    <label for="star5">★</label>
                    <input type="radio" id="star4" name="rating" value="4" <?php if ($existingRating && $existingRating['rating'] == 4) echo 'checked'; ?>>
                    <label for="star4">★</label>
                    <input type="radio" id="star3" name="rating" value="3" <?php if ($existingRating && $existingRating['rating'] == 3) echo 'checked'; ?>>
                    <label for="star3">★</label>
                    <input type="radio" id="star2" name="rating" value="2" <?php if ($existingRating && $existingRating['rating'] == 2) echo 'checked'; ?>>
                    <label for="star2">★</label>
                    <input type="radio" id="star1" name="rating" value="1" <?php if ($existingRating && $existingRating['rating'] == 1) echo 'checked'; ?>>
                    <label for="star1">★</label>
                </div>
            </form>
        <?php else: ?>
            <p>Please <a href="login.php">login</a> to rate this work.</p>
        <?php endif; ?>

        <!-- Jika pengguna adalah penulis karya -->
        <?php if ($isAuthor): ?>
            <a href="edit_work.php?work_id=<?php echo $work['id']; ?>" class="btn btn-primary">Edit Work</a>
            <a href="add_chapter.php?work_id=<?php echo $work['id']; ?>" class="btn btn-success">Add Chapter</a>
        <?php endif; ?>

        <h2>Chapters</h2>
        <?php
        // Ambil chapter terkait karya ini
        $query = "SELECT * FROM Chapters WHERE work_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $workId);
        $stmt->execute();
        $chaptersResult = $stmt->get_result();

        $chapters = [];
        while ($chapter = $chaptersResult->fetch_assoc()) {
            $chapters[] = $chapter;
        }

        $totalChapters = count($chapters);
        ?>

        <?php if ($totalChapters > 0): ?>
            <ul class="list-group">
                <?php 
                $counter = $totalChapters;
                foreach ($chapters as $chapter):
                ?>
                    <li class="list-group-item">
                        <span><?php echo $counter--; ?>. </span>
                        <a href="view_chapter.php?chapter_id=<?php echo $chapter['id']; ?>" class="chapter-link">
                            <?php echo htmlspecialchars($chapter['name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No chapters available.</p>
        <?php endif; ?>
    </div>

    <div>
        <!-- Tombol Report pada karya -->
        <button type="button" class="report-btn btn-danger" data-type="work" data-id="<?php echo $work['id']; ?>" data-user-id="<?php echo $work['author_id']; ?>">Report Work</button>
    </div>

    <script>
    // Notifikasi muncul otomatis jika ada pesan
    <?php if ($notificationMessage): ?>
        document.getElementById('notification').style.display = 'block';
        setTimeout(() => {
            document.getElementById('notification').style.display = 'none';
        }, 1200);
    <?php endif; ?>

    document.querySelectorAll('.report-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const type = this.getAttribute('data-type'); 
            const reportedId = this.getAttribute('data-id');
            const reportedUserId = this.getAttribute('data-user-id');
            const reason = prompt("Enter the reason for reporting:");

            if (reason) {
                fetch('report.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        type: type,
                        reportedId: reportedId,
                        reportedUserId: reportedUserId,
                        reason: reason
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                });
            }
        });
    });
    </script>

    <script>
    // Notifikasi muncul otomatis jika ada pesan
    <?php if ($notificationMessage): ?>
        document.getElementById('notification').style.display = 'block';
        setTimeout(function() {
            document.getElementById('notification').style.display = 'none';
        }, 1200);
    <?php endif; ?>

    // Kirim formulir rating saat bintang diklik
    document.querySelectorAll('.stars input').forEach(star => {
        star.addEventListener('change', function() {
            document.getElementById('ratingForm').submit();
        });
    });

    // Logika bintang hover
    document.querySelectorAll('.stars label').forEach(star => {
        star.addEventListener('mouseover', function () {
            let currentStarId = this.getAttribute('for');
            document.querySelectorAll('.stars label').forEach(s => {
                if (s.getAttribute('for') <= currentStarId) {
                    s.style.color = 'gold';
                } else {
                    s.style.color = 'gray';
                }
            });
        });
    });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
