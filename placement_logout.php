<?php
session_start();
session_destroy();
header("Location: placement_officer_login.php");
exit();
?>
