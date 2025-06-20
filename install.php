<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $dbname = $_POST['dbname'];
    $sqlFile = __DIR__ . '/gestion.sql';

// Création du fichier config.php
$config = "<?php
define('DB_HOST', '$host');
define('DB_USER', '$user');
define('DB_PASS', '$pass');
define('DB_NAME', '$dbname');

// Connexion PDO
function getPDO() {
    static \$pdo = null;
    if (\$pdo === null) {
        \$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            \$pdo = new PDO(\$dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException \$e) {
            die('Erreur de connexion PDO : ' . \$e->getMessage());
        }
    }
    return \$pdo;
}
?>";
file_put_contents('config.php', $config);
    // Import SQL
    $mysqli = new mysqli($host, $user, $pass);
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci");
    $mysqli->select_db($dbname);
    $sql = file_get_contents('gestion.sql'); // Chemin du fichier SQL
    $mysqli->multi_query($sql);

    echo "<b>Installation terminée !</b>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Installateur simple</title>
</head>
<body>
    <h1>Installateur Fab-Track</h1>
    <form method="post">
        <label>Hôte MySQL : <input name="host" value="localhost" required></label><br>
        <label>Utilisateur : <input name="user" value="root" required></label><br>
        <label>Mot de passe : <input name="pass" type="password"></label><br>
        <label>Base de données : <input name="dbname" value="gestion" required></label><br>
        <button type="submit">Installer</button>
    </form>
</body>
</html>
