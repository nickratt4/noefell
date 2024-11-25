<?php
require '../config/database.php'; // Memperbarui path ke database
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <link rel="stylesheet" href="../includes/style.css"> <!-- Memperbarui path ke style -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Noefell</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark ">
  <a class="navbar-brand" href="../index.php">Noefell</a> <!-- Memperbarui path ke index -->

  <!-- Form Pencarian untuk layar besar -->
  <form class="form-inline my-2 my-lg-0 d-none d-lg-flex" method="GET" action="../search.php"> <!-- Memperbarui path ke search -->
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
        <a class="nav-link" href="../index.php">Home</a> <!-- Memperbarui path ke index -->
      </li>
      <!-- Form Pencarian untuk layar kecil -->
      <form class="form-inline my-2 my-lg-0 d-lg-none w-100" method="GET" action="../search.php"> <!-- Memperbarui path ke search -->
        <input class="form-control mr-sm-2 w-75" type="search" name="search" placeholder="Cari karya atau kategori..." aria-label="Search" required>
        <button class="btn btn-outline-success my-2 my-sm-0 w-25" type="submit">Cari</button>
      </form>
      
      <!-- Jika user login, tampilkan menu user -->
      <?php if ($user): ?>
        <li class="nav-item">
          <a class="nav-link" href="../profile.php">Profile</a> <!-- Memperbarui path ke profile -->
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../history.php">History</a> <!-- Memperbarui path ke history -->
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../user_works.php">My Works</a> <!-- Memperbarui path ke user_works -->
        </li>
        
        <!-- Logout Trigger -->
        <li class="nav-item">
          <a href="#" id="logout-link" class="nav-link">Logout (<?php echo htmlspecialchars($user['username']); ?>)</a>
        </li>

        <!-- Menu Admin -->
        <?php if ($isAdmin): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="adminMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Admin Panel
            </a>
            <div class="dropdown-menu" aria-labelledby="adminMenu">
              <a class="dropdown-item" href="../admin/manage_reports.php">Manage Reports</a> <!-- Memperbarui path ke admin/manage_reports -->
              <a class="dropdown-item" href="../admin/user_ban.php">List User Ban</a> <!-- Memperbarui path ke admin/user_ban -->
              <a class="dropdown-item" href="../admin/manage_categories.php">Manage Categories</a> <!-- Memperbarui path ke admin/manage_categories -->
            </div>
          </li>
        <?php endif; ?>

        <li class="nav-item">
          <a class="nav-link" href="../profile.php"> <!-- Memperbarui path ke profile -->
            <img src="../uploads/<?php echo htmlspecialchars($user['profile_picture'] ?: 'default_profile.jpg'); ?>" alt="Profile Picture" style="width:50px;height:50px;border-radius:50%;"> <!-- Memperbarui path ke uploads -->
          </a>
        </li>
        
      <?php else: ?>
        <li class="nav-item">
          <a class="nav-link" href="../login.php">Login</a> <!-- Memperbarui path ke login -->
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../register.php">Register</a> <!-- Memperbarui path ke register -->
        </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<!-- Include Modal Logout -->
<?php include '../includes/logout_modal.php'; ?>

<script>
    // Get the modal and logout link
    const modal = document.getElementById('logoutModal');
    const logoutLink = document.getElementById('logout-link');

    // Show modal on logout link click
    logoutLink.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent default link behavior
        modal.style.display = 'block'; // Show modal
    });

    // Confirm logout
    function confirmLogout() {
        window.location.href = '../logout.php'; // Redirect to logout script
    }

    // Cancel logout
    function hideLogoutModal() {
        modal.style.display = 'none'; // Hide modal
    }
</script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
