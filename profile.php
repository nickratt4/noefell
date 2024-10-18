<?php
require 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$query = "SELECT * FROM Users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = $_POST['bio'];
    $profilePicture = $_FILES['profile_picture']['name'];
    $profilePictureTemp = $_FILES['profile_picture']['tmp_name'];

    if ($profilePicture) {
        move_uploaded_file($profilePictureTemp, "uploads/" . $profilePicture);
    } else {
        $profilePicture = $user['profile_picture'];
    }

    $query = "UPDATE Users SET bio = ?, profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $bio, $profilePicture, $userId);

    if ($stmt->execute()) {
        header("Location: profile.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Noefell</title>
    <link rel="stylesheet" href="includes/Profile.css">
    <style>

    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="profil">
        <div class="foto-profil">
            <?php if (!empty($user['profile_picture'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            <?php else: ?>
                <img src="uploads/default-profile.png" alt="Default Profile Picture">
            <?php endif; ?>
        </div>

        <div class="informasi-profil">
            <h3><?php echo htmlspecialchars($user['username']); ?></h3>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($user['bio']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <input type="file" class="form-control-file" id="profile_picture" name="profile_picture">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
