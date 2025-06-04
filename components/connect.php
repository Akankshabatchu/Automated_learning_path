<?php

$db_name = 'mysql:host=localhost;port=3307;dbname=my_project_db'; 
$user_name = 'root';
$user_password = '';

try {
    $conn = new PDO($db_name, $user_name, $user_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

if (!function_exists('unique_id')) {
    function unique_id() {
       $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
       $rand = array();
       $length = strlen($str) - 1;
       for ($i = 0; $i < 20; $i++) {
           $n = mt_rand(0, $length);
           $rand[] = $str[$n];
       }
       return implode($rand);
    }
}

?>
