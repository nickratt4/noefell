<?php
session_start();
session_unset();
session_destroy();
header("Location: index.php?../toast.php?type=green&message=Logout%20successful!");
exit();
?>
