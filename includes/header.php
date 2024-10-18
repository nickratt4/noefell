<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config/database.php';

$user = null;
$isAdmin = false; // Variabel untuk cek apakah user adalah admin

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $query = "SELECT * FROM Users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Cek apakah user adalah admin
    if (isset($user['role']) && $user['role'] === 'admin') {
        $isAdmin = true;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="includes/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Noefell</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark ">
  <a class="navbar-brand" href="index.php">Noefell</a>

  <!-- Form Pencarian untuk layar besar -->
  <form class="form-inline my-2 my-lg-0 d-none d-lg-flex" method="GET" action="search.php">
    <input class="form-control mr-sm-2" type="search" name="search" placeholder="Cari karya atau kategori..." aria-label="Search" required>
    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Cari</button>
  </form>

  <!-- Tombol untuk navbar collapse pada layar kecil -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <!-- Konten navbar yang collapse -->
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"> 
        <a class="nav-link" href="index.php">Home</a>
      </li>
      <!-- Form Pencarian untuk layar kecil -->
      <form class="form-inline my-2 my-lg-0 d-lg-none w-100" method="GET" action="search.php">
        <input class="form-control mr-sm-2 w-75" type="search" name="search" placeholder="Cari karya atau kategori..." aria-label="Search" required>
        <button class="btn btn-outline-success my-2 my-sm-0 w-25" type="submit">Cari</button>
      </form>
      
      <!-- Jika user login, tampilkan menu user -->
      <?php if ($user): ?>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="user_works.php">My Works</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout (<?php echo htmlspecialchars($user['username']); ?>)</a>
        </li>
   <!-- Menu Admin -->
   <?php if ($isAdmin): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="adminMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Admin Panel
            </a>
            <div class="dropdown-menu" aria-labelledby="adminMenu">
              <a class="dropdown-item" href="admin/manage_reports.php">Manage Reports</a>
              <a class="dropdown-item" href="admin/user_ban.php">list User Ban</a>
              <a class="dropdown-item" href="admin/manage_categories.php">Manage Categories</a>
              <!--fitur admin  -->
            </div>
          </li>
        <?php endif; ?> 

        <li class="nav-item">
          <a class="nav-link" href="profile.php">
            <img src="uploads/<?php echo htmlspecialchars($user['profile_picture'] ?: 'default_profile.jpg'); ?>" alt="Profile Picture" style="width:50px;height:50px;border-radius:50%;">
          </a>
        </li>
        
      <?php else: ?>
        <li class="nav-item">
          <a class="nav-link" href="login.php">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="register.php">Register</a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>



<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
