<?php
require_once 'config.php';
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
} catch (PDOException $e) {
    exit('erreur lors de la connection : ' . $e->getMessage());
}
?>