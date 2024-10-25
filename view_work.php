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

// Cek urutan pengurutan dari query string, default "DESC"
$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

// Ambil chapter terkait karya ini, urutkan berdasarkan `id` sesuai dengan pilihan
$query = "SELECT * FROM Chapters WHERE work_id = ? ORDER BY id $order";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $workId);
$stmt->execute();
$chaptersResult = $stmt->get_result();

$chapters = [];
while ($chapter = $chaptersResult->fetch_assoc()) {
    $chapters[] = $chapter;
}
$totalChapters = count($chapters);
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

        .chapter-sort-icon {
            font-size: 1.2em;
            cursor: pointer;
        }

        .chapter-sort-icon.asc::before {
            content: "▲";
        }

        .chapter-sort-icon.desc::before {
            content: "▼";
        }
         /* Gaya untuk tabel */
         body {
        font-family: Arial, sans-serif;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px dotted #ddd;
    }
    th {
        background-color: #f2f2f2;
    }
    a {
        color: blue; /* Warna tautan saat belum diklik */
        text-decoration: none;
    }
    a.clicked {
        color: black; /* Warna tautan saat sudah diklik */
    }
    a:hover {
        text-decoration: underline;
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
        <?php include 'includes/ratting.php' ?>


        <!-- Jika pengguna adalah penulis karya -->
        <?php if ($isAuthor): ?>
            <a href="edit_work.php?work_id=<?php echo $work['id']; ?>" class="btn btn-primary">Edit Work</a>
            <a href="add_chapter.php?work_id=<?php echo $work['id']; ?>" class="btn btn-success">Add Chapter</a>
        <?php endif; ?>

        <h2>
    Chapters
    <a href="?work_id=<?php echo $workId; ?>&order=<?php echo $order === 'ASC' ? 'desc' : 'asc'; ?>">
        <span class="chapter-sort-icon <?php echo $order === 'ASC' ? 'asc' : 'desc'; ?>"></span>
    </a>
</h2>

<?php if ($totalChapters > 0): ?>
    <table>
        <tr>
            <th>Chapter</th>
            <th>Date</th>
        </tr>
        <?php 
        // Hitung ulang counter sesuai dengan urutan pengurutan
        $counter = $order === 'ASC' ? 1 : $totalChapters;
        foreach ($chapters as $chapter):
            $date = date('d/m/Y', strtotime($chapter['created_at'])); // Sesuaikan dengan nama kolom tanggal di tabel chapters
        ?>
            <tr>
                <td>
                    <a href="view_chapter.php?chapter_id=<?php echo $chapter['id']; ?>" 
                       onclick="markAsClicked(event)">
                        <?php echo $counter . ". " . htmlspecialchars($chapter['name']); // Menambahkan nomor urut ?>
                    </a>
                </td>
                <td><?php echo $date; ?></td>
            </tr>
        <?php 
            // Update counter berdasarkan urutan pengurutan
            $counter += $order === 'ASC' ? 1 : -1; 
        endforeach; 
        ?>
    </table>
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
        // Tampilkan notifikasi jika ada pesan
        <?php if (!empty($notificationMessage)): ?>
            document.getElementById("notification").style.display = "block";
            setTimeout(function() {
                document.getElementById("notification").style.display = "none";
            }, 3000);
        <?php endif; ?>

        // Submit rating secara otomatis setelah klik bintang
        document.querySelectorAll('.stars input').forEach(input => {
            input.addEventListener('change', function() {
                document.getElementById('ratingForm').submit();
            });
        });
    </script>

<script>
    function markAsClicked(event) {
        event.target.classList.add('clicked'); // Menambahkan kelas clicked saat tautan diklik
    }
</script>


    <?php include 'includes/footer.php'; ?>
</body>
</html>
