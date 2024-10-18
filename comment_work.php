<?php
include 'config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $work_id = $_POST['work_id'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO Comments (work_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$work_id, $user_id, $comment]);

    header("Location: view_work.php?id=$work_id");
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Berikan Komentar</h2>
    <form action="comment_work.php" method="post">
        <input type="hidden" name="work_id" value="<?= $_GET['work_id'] ?>">
        <div class="mb-3">
            <label for="comment" class="form-label">Komentar</label>
            <textarea class="form-control" id="comment" name="comment" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Kirim Komentar</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
