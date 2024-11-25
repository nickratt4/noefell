<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config/database.php';

// Menggunakan prefix 'noefel_' untuk variabel
$noefel_user = null;
$noefel_isAdmin = false; // Variabel untuk cek apakah user adalah admin

if (isset($_SESSION['user_id'])) {
    $noefel_userId = $_SESSION['user_id'];
    $noefel_query = "SELECT * FROM Users WHERE id = ?";
    $noefel_stmt = $conn->prepare($noefel_query);
    $noefel_stmt->bind_param("i", $noefel_userId);
    $noefel_stmt->execute();
    $noefel_result = $noefel_stmt->get_result();
    $noefel_user = $noefel_result->fetch_assoc();
    $noefel_stmt->close();

    // Cek apakah user adalah admin
    if (isset($noefel_user['role']) && $noefel_user['role'] === 'admin') {
        $noefel_isAdmin = true;
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
      <?php if ($noefel_user): ?>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="history.php">History</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="user_works.php">My Works</a>
        </li>
        <!-- Tombol Logout dengan konfirmasi -->
        <li class="nav-item">
          <a class="nav-link" href="#" id="logout-link">Logout (<?php echo htmlspecialchars($noefel_user['username']); ?>)</a>
        </li>

        <!-- Menu Admin -->
        <?php if ($noefel_isAdmin): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="adminMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Admin Panel
            </a>
            <div class="dropdown-menu" aria-labelledby="adminMenu">
              <a class="dropdown-item" href="admin/manage_reports.php">Manage Reports</a>
              <a class="dropdown-item" href="admin/user_ban.php">List User Ban</a>
              <a class="dropdown-item" href="admin/manage_categories.php">Manage Categories</a>
            </div>
          </li>
        <?php endif; ?> 

        <li class="nav-item">
          <a class="nav-link" href="profile.php">
            <img src="uploads/<?php echo htmlspecialchars($noefel_user['profile_picture'] ?: 'default_profile.jpg'); ?>" alt="Profile Picture" style="width:50px;height:50px;border-radius:50%;">
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

<?php include 'logout_modal.php'; ?>


<!-- Modal Logout -->
<div class="modal" id="logoutModal">
    <h2>Logout Confirmation</h2>
    <p>Are you sure you want to logout?</p>
    <button class="confirm" onclick="confirmLogout()">Logout</button>
    <button class="cancel" onclick="cancelLogout()">Cancel</button>
</div>

<script>
    // Get modal and logout link
    const modal = document.getElementById('logoutModal');
    const logoutLink = document.getElementById('logout-link');

    // Show modal on logout link click
    logoutLink.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent default link behavior
        modal.style.display = 'block'; // Show the modal
    });

    // Confirm logout
    function confirmLogout() {
        window.location.href = 'logout.php'; // Redirect to logout script
    }

    // Cancel logout
    function cancelLogout() {
        modal.style.display = 'none'; // Hide the modal
    }
</script>

</body>
</html>
