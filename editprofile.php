<?php
include 'config/database.php';
session_start();

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = $_POST['bio'];

    $stmt = $pdo->prepare("UPDATE Users SET bio = ? WHERE id = ?");
    $stmt->execute([$bio, $user_id]);

    header("Location: profile.php");
    exit;
}

$stmt = $pdo->prepare("SELECT bio FROM Users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Edit Profil</h2>
    <form action="edit_profile.php" method="post">
        <div class="mb-3">
            <label for="bio" class="form-label">Bio</label>
            <textarea class="form-control" id="bio" name="bio" rows="4" required><?= htmlspecialchars($user['bio']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
