<?php
// Add any commonly used functions here

function validateInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function checkUserExists($conn, $username) {
    $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
?>
