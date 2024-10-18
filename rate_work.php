<?php
include 'config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $work_id = $_POST['work_id'];
    $rating = $_POST['rating'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO Ratings (work_id, user_id, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = ?");
    $stmt->execute([$work_id, $user_id, $rating, $rating]);

    header("Location: view_work.php?id=$work_id");
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Berikan Rating</h2>
    <form action="rate_work.php" method="post">
        <input type="hidden" name="work_id" value="<?= $_GET['work_id'] ?>">
        <div class="mb-3">
            <label for="rating" class="form-label">Rating (1-5)</label>
            <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
        </div>
        <button type="submit" class="btn btn-primary">Kirim Rating</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
