<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $query = "SELECT * FROM Users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        $hashedPassword = $user['password'];
        if (password_verify($password, $hashedPassword)) {
            $now = new DateTime();
            if ($user['banned_until'] && new DateTime($user['banned_until']) > $now) {
                echo "Your account is banned until " . htmlspecialchars($user['banned_until']);
                exit;
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Login - Novel Website</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="includes/login.css">
</head>
<body>
<div class="login-form">
    <form action="login.php" method="POST">
        <h2 class="text-center">Login</h2>   
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <div class="form-group">
            <input type="text" class="form-control" name="username" placeholder="Username" required="required">
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password" required="required">
        </div>        
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>
        </div>

    </form>
    <p class="text-center small">Don't have an account? <a href="register.php ">Sign up here!</a></p>
</div>


</body>

</html>
