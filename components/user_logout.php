<?php
setcookie('user_id', '', time() - 3600, '/'); // Expire the cookie
header('Location: ../login.php'); // Redirect to login page
exit;
?>
