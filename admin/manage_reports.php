<?php
session_start();
require '../config/database.php';

// Mengambil laporan
$adm_query = "SELECT r.*, u.username AS reporter_name, 
              (CASE r.report_type 
               WHEN 'work' THEN w.title 
               WHEN 'comment' THEN c.content 
               END) AS reported_item, 
              (CASE r.report_type 
               WHEN 'work' THEN w.author_id 
               WHEN 'comment' THEN c.user_id 
               END) AS reported_user_id,
              r.report_type, r.reported_id
              FROM reports r
              LEFT JOIN users u ON r.reporter_id = u.id
              LEFT JOIN works w ON r.report_type = 'work' AND r.reported_id = w.id
              LEFT JOIN comments c ON r.report_type = 'comment' AND r.reported_id = c.id
              ORDER BY r.reported_at DESC";
$adm_result = $conn->query($adm_query);

// Proses ban pengguna
if (isset($_POST['ban_user'])) {
    $adm_userId = intval($_POST['user_id']);
    $adm_banDuration = intval($_POST['ban_duration']); // Durasi ban dalam hari
    $adm_banReason = trim($_POST['ban_reason']);
    $adm_banReportLocation = trim($_POST['report_location']); // Lokasi dari laporan

    // Hitung tanggal berakhir ban
    $adm_banUntil = date('Y-m-d H:i:s', strtotime("+$adm_banDuration days"));

    // Update data pengguna di tabel `users` dan tambahkan ke `bans`
    $adm_updateUserQuery = "UPDATE Users SET banned_until = ?, ban_reason = ? WHERE id = ?";
    $adm_updateUserStmt = $conn->prepare($adm_updateUserQuery);
    $adm_updateUserStmt->bind_param("ssi", $adm_banUntil, $adm_banReason, $adm_userId);
    $adm_updateUserStmt->execute();

    // Hapus laporan terkait pengguna dari tabel `reports`
    $adm_deleteReportQuery = "DELETE FROM Reports WHERE reported_user_id = ?";
    $adm_deleteReportStmt = $conn->prepare($adm_deleteReportQuery);
    $adm_deleteReportStmt->bind_param("i", $adm_userId);
    $adm_deleteReportStmt->execute();

    $_SESSION['success_message'] = "User has been banned successfully from the report location: $adm_banReportLocation!";
    header('Location: manage_reports.php');
    exit;
}

// Proses penghapusan item
if (isset($_POST['delete_item'])) {
    $adm_itemId = intval($_POST['item_id']);
    $adm_itemType = $_POST['item_type']; // 'work' atau 'comment'

    // Jika item adalah 'work', hapus dulu rating, komentar, dan riwayat baca yang terkait
    if ($adm_itemType === 'work') {
        // Hapus rating yang terkait dengan work ini
        $adm_deleteRatingsQuery = "DELETE FROM ratings WHERE work_id = ?";
        $adm_deleteRatingsStmt = $conn->prepare($adm_deleteRatingsQuery);
        $adm_deleteRatingsStmt->bind_param("i", $adm_itemId);
        $adm_deleteRatingsStmt->execute();

        // Hapus komentar yang terkait dengan work ini
        $adm_deleteCommentsQuery = "DELETE FROM comments WHERE work_id = ?";
        $adm_deleteCommentsStmt = $conn->prepare($adm_deleteCommentsQuery);
        $adm_deleteCommentsStmt->bind_param("i", $adm_itemId);
        $adm_deleteCommentsStmt->execute();
        
        // Hapus riwayat baca yang terkait dengan work ini
        $adm_deleteReadingHistoryQuery = "DELETE rh FROM readinghistory rh 
                                          JOIN chapters ch ON rh.chapter_id = ch.id 
                                          WHERE ch.work_id = ?";
        $adm_deleteReadingHistoryStmt = $conn->prepare($adm_deleteReadingHistoryQuery);
        $adm_deleteReadingHistoryStmt->bind_param("i", $adm_itemId);
        $adm_deleteReadingHistoryStmt->execute();
    }

    // Hapus item (work atau comment)
    if ($adm_itemType === 'work') {
        $adm_deleteItemQuery = "DELETE FROM works WHERE id = ?";
    } else {
        $adm_deleteItemQuery = "DELETE FROM comments WHERE id = ?";
    }

    $adm_deleteItemStmt = $conn->prepare($adm_deleteItemQuery);
    $adm_deleteItemStmt->bind_param("i", $adm_itemId);
    if ($adm_deleteItemStmt->execute()) {
        // Hapus laporan terkait
        $adm_deleteReportQuery = "DELETE FROM Reports WHERE reported_id = ? AND report_type = ?";
        $adm_deleteReportStmt = $conn->prepare($adm_deleteReportQuery);
        $adm_deleteReportStmt->bind_param("is", $adm_itemId, $adm_itemType);
        $adm_deleteReportStmt->execute();

        $_SESSION['success_message'] = "Item has been deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to delete item: " . $conn->error;
    }
    header('Location: manage_reports.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reports</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php require '../includes/navbar_admin.php'; ?>

<div class="container mt-5">
    <h2>Manage Reports</h2>
    <!-- Menampilkan pesan sukses atau error -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Tabel Laporan -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Reporter</th>
                <th>Reported Item</th>
                <th>Type</th>
                <th>Reason</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($adm_report = $adm_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($adm_report['reporter_name']); ?></td>
                    <td>
                        <!-- Jika laporan adalah work, buat tautan ke halaman detail work -->
                        <?php if ($adm_report['report_type'] === 'work'): ?>
                            <a href="../view_work.php?work_id=<?php echo $adm_report['reported_id']; ?>" target="_blank">
                                <?php echo htmlspecialchars($adm_report['reported_item']); ?>
                            </a>
                        <?php else: ?>
                            <?php echo htmlspecialchars($adm_report['reported_item']); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($adm_report['report_type']); ?></td>
                    <td><?php echo htmlspecialchars($adm_report['reason']); ?></td>
                    <td>
                        <!-- Tombol Ban -->
                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#banModal" 
                                data-user-id="<?php echo $adm_report['reported_user_id']; ?>"
                                data-report-location="<?php echo ($adm_report['report_type'] === 'work') ? 'Work: ' . htmlspecialchars($adm_report['reported_item']) : 'Comment'; ?>">
                            Ban
                        </button>
                        <!-- Tombol Delete -->
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#deleteModal" 
                                data-item-id="<?php echo $adm_report['reported_id']; ?>" 
                                data-item-type="<?php echo $adm_report['report_type']; ?>">
                            Delete
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Ban Modal -->
<div class="modal fade" id="banModal" tabindex="-1" role="dialog" aria-labelledby="banModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="banModalLabel">Ban User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="ban_user_id">
                    <div class="form-group">
                        <label for="ban_duration">Ban Duration (days):</label>
                        <input type="number" name="ban_duration" id="ban_duration" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="ban_reason">Reason:</label>
                        <textarea name="ban_reason" id="ban_reason" class="form-control" required></textarea>
                    </div>
                    <input type="hidden" name="report_location" id="ban_report_location">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="ban_user" class="btn btn-danger">Ban User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="item_id" id="delete_item_id">
                    <input type="hidden" name="item_type" id="delete_item_type">
                    <p>Are you sure you want to delete this item?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_item" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->

<script src="../includes/MR.js">

</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>


</body>
</html>
    