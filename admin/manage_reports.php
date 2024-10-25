<?php
session_start();
require '../config/database.php';
require '../includes/navbar_admin.php';

// Mengambil laporan
$query = "SELECT r.*, u.username AS reporter_name, 
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
$result = $conn->query($query);

// Proses ban pengguna
if (isset($_POST['ban_user'])) {
    $userId = intval($_POST['user_id']);
    $banDuration = intval($_POST['ban_duration']); // Durasi ban dalam hari
    $banReason = trim($_POST['ban_reason']);
    $banReportLocation = trim($_POST['report_location']); // Lokasi dari laporan

    // Hitung tanggal berakhir ban
    $banUntil = date('Y-m-d H:i:s', strtotime("+$banDuration days"));

    // Update data pengguna di tabel `users` dan tambahkan ke `bans`
    $updateUserQuery = "UPDATE Users SET banned_until = ?, ban_reason = ? WHERE id = ?";
    $updateUserStmt = $conn->prepare($updateUserQuery);
    $updateUserStmt->bind_param("ssi", $banUntil, $banReason, $userId);
    $updateUserStmt->execute();

    // Hapus laporan terkait pengguna dari tabel `reports`
    $deleteReportQuery = "DELETE FROM Reports WHERE reported_user_id = ?";
    $deleteReportStmt = $conn->prepare($deleteReportQuery);
    $deleteReportStmt->bind_param("i", $userId);
    $deleteReportStmt->execute();

    $_SESSION['success_message'] = "User has been banned successfully from the report location: $banReportLocation!";
    header('Location: manage_reports.php');
    exit;
}
// Proses penghapusan item
if (isset($_POST['delete_item'])) {
    $itemId = intval($_POST['item_id']);
    $itemType = $_POST['item_type']; // 'work' atau 'comment'

    // Jika item adalah 'work', hapus dulu comment yang terkait
   // Jika item adalah 'work', hapus dulu ratings yang terkait
if ($itemType === 'work') {
    // Hapus dulu rating yang terkait dengan work ini
    $deleteRatingsQuery = "DELETE FROM ratings WHERE work_id = ?";
    $deleteRatingsStmt = $conn->prepare($deleteRatingsQuery);
    $deleteRatingsStmt->bind_param("i", $itemId);
    $deleteRatingsStmt->execute();

    // Hapus juga komentar yang terkait dengan work ini
    $deleteCommentsQuery = "DELETE FROM comments WHERE work_id = ?";
    $deleteCommentsStmt = $conn->prepare($deleteCommentsQuery);
    $deleteCommentsStmt->bind_param("i", $itemId);
    $deleteCommentsStmt->execute();
}


    // Proses penghapusan item
    if ($itemType === 'work') {
        $deleteItemQuery = "DELETE FROM works WHERE id = ?";
    } else {
        $deleteItemQuery = "DELETE FROM comments WHERE id = ?";
    }

    $deleteItemStmt = $conn->prepare($deleteItemQuery);
    $deleteItemStmt->bind_param("i", $itemId);
    if ($deleteItemStmt->execute()) {
        // Hapus laporan terkait
        $deleteReportQuery = "DELETE FROM Reports WHERE reported_id = ? AND report_type = ?";
        $deleteReportStmt = $conn->prepare($deleteReportQuery);
        $deleteReportStmt->bind_param("is", $itemId, $itemType);
        $deleteReportStmt->execute();

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
                <?php while ($report = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['reporter_name']); ?></td>
                        <td>
                            <!-- Jika laporan adalah work, buat tautan ke halaman detail work -->
                            <?php if ($report['report_type'] === 'work'): ?>
                                <a href="../view_work.php?work_id=<?php echo $report['reported_id']; ?>" target="_blank">
                                    <?php echo htmlspecialchars($report['reported_item']); ?>
                                </a>
                            <?php else: ?>
                                <?php echo htmlspecialchars($report['reported_item']); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($report['report_type']); ?></td>
                        <td><?php echo htmlspecialchars($report['reason']); ?></td>
                        <td>
                            <!-- Tombol Ban -->
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#banModal" 
                                    data-user-id="<?php echo $report['reported_user_id']; ?>"
                                    data-report-location="<?php echo ($report['report_type'] === 'work') ? 'Work: ' . htmlspecialchars($report['reported_item']) : 'Comment'; ?>">
                                Ban
                            </button>
                            <!-- Tombol Delete -->
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#deleteModal" 
                                    data-item-id="<?php echo $report['reported_id']; ?>" 
                                    data-item-type="<?php echo $report['report_type']; ?>">
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
                        <div class="form-group">
                            <label for="report_location">Report Location:</label>
                            <input type="text" name="report_location" id="report_location" class="form-control" readonly>
                        </div>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="delete_item" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#banModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var userId = button.data('user-id');
            var reportLocation = button.data('report-location');
            var modal = $(this);
            modal.find('#ban_user_id').val(userId);
            modal.find('#report_location').val(reportLocation);
        });

        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var itemId = button.data('item-id');
            var itemType = button.data('item-type');
            var modal = $(this);
            modal.find('#delete_item_id').val(itemId);
            modal.find('#delete_item_type').val(itemType);
        });
    </script>
</body>
</html>
