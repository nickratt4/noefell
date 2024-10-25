<?php
session_start();
require '../config/database.php';
include"../includes/navbar_admin.php";



// Ambil data pengguna yang di-ban dari tabel `users`
$query = "SELECT * FROM Users WHERE banned_until IS NOT NULL AND banned_until > NOW()";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banned Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Banned Users</h2>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Banned Until</th>
                        <th>Ban Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['banned_until']); ?></td>
                            <td><?php echo htmlspecialchars($user['ban_reason']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No banned users found.</p>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script> <!-- Pastikan jQuery ini sebelum Bootstrap -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>

