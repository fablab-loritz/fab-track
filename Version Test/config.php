<?php

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'gestion');
define('DB_USER', 'root');
define('DB_PASS', '');
function getPDO() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME,
                DB_USER,
                DB_PASS
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit('Erreur de connexion PDO : ' . $e->getMessage());
        }
    }
    return $pdo;
}

// Connexion MySQLi
function getMySQLi() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        die('Erreur de connexion MySQLi : ' . $conn->connect_error);
    }
    return $conn;
}
?>
