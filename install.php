<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $dbname = $_POST['dbname'];

    // Télécharger le zip depuis GitHub (lien raw)
    $zipUrl = 'https://github.com/fablab-loritz/fab-track/raw/main/Fab-Track.zip';
    $zipFile = __DIR__ . '/Fab-Track.zip';

    $zipContent = @file_get_contents($zipUrl);
    if ($zipContent === false) {
        die("Erreur lors du téléchargement du zip depuis GitHub.<br>Vérifie l'URL : $zipUrl");
    }
    file_put_contents($zipFile, $zipContent);

    // Décompresser le zip
    $zip = new ZipArchive();
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo(__DIR__);
        $zip->close();
        // Suppression robuste du zip
        $try = 0;
        while (file_exists($zipFile) && $try < 5) {
            if (@unlink($zipFile)) break;
            usleep(200000);
            $try++;
        }
        if (file_exists($zipFile)) {
            echo "<b>Attention :</b> Impossible de supprimer Fab-Track.zip. Veuillez le supprimer manuellement.<br>";
        }
    } else {
        die("Erreur lors de l'extraction du zip.");
    }

    // Vérifier la présence du fichier SQL
    $sqlFile = __DIR__ . '/gestion.sql';
    if (!file_exists($sqlFile)) {
        die("Le fichier gestion.sql est introuvable après extraction.<br>Vérifie le contenu du zip.");
    }

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
    file_put_contents(__DIR__ . '/config.php', $config);

    // Import SQL
    $mysqli = new mysqli($host, $user, $pass);
    if ($mysqli->connect_errno) {
        die("Erreur de connexion MySQL : " . $mysqli->connect_error);
    }
    $mysqli->query("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci");
    $mysqli->select_db($dbname);
    $sql = file_get_contents($sqlFile);
    if (!$sql) {
        die("Erreur lors de la lecture du fichier SQL.");
    }
    if (!$mysqli->multi_query($sql)) {
        die("Erreur lors de l'import SQL : " . $mysqli->error);
    }

    echo "<b>Installation terminée !</b>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Installateur Fab-Track</title>
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