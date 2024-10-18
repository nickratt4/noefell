<?php
include 'config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $work_id = $_POST['work_id'];
    $report_message = $_POST['report_message'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO Reports (work_id, user_id, report_message) VALUES (?, ?, ?)");
    $stmt->execute([$work_id, $user_id, $report_message]);

    header("Location: view_work.php?id=$work_id");
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Laporkan Karya</h2>
    <form action="report_work.php" method="post">
        <input type="hidden" name="work_id" value="<?= $_GET['work_id'] ?>">
        <div class="mb-3">
            <label for="report_message" class="form-label">Pesan Laporan</label>
            <textarea class="form-control" id="report_message" name="report_message" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Kirim Laporan</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
