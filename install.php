<?php
// Vérifie que le script est exécuté en CLI
if (php_sapi_name() !== 'cli') {
    die("Ce script doit être exécuté en ligne de commande.\n");
}

// Lire les entrées utilisateur
function prompt($text, $default = '') {
    $value = readline($text . ($default ? " [$default]" : '') . ": ");
    return $value !== '' ? $value : $default;
}

$host = prompt("Hôte MySQL", "localhost");
$user = prompt("Utilisateur MySQL", "root");
$pass = prompt("Mot de passe MySQL");
$dbname = prompt("Nom de la base de données", "gestion");

// Télécharger le zip depuis GitHub
$zipUrl = 'https://github.com/fablab-loritz/fab-track/raw/main/Fab-Track.zip';
$zipFile = __DIR__ . '/Fab-Track.zip';

echo "Téléchargement de l'archive depuis GitHub...\n";
$zipContent = @file_get_contents($zipUrl);
if ($zipContent === false) {
    die("Erreur lors du téléchargement du zip depuis GitHub. Vérifie l'URL : $zipUrl\n");
}
file_put_contents($zipFile, $zipContent);

// Décompression
echo "Extraction de l'archive...\n";
$zip = new ZipArchive();
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo(__DIR__);
    $zip->close();
    unlink($zipFile);
} else {
    die("Erreur lors de l'extraction du zip.\n");
}

// Vérification SQL
$sqlFile = __DIR__ . '/gestion.sql';
if (!file_exists($sqlFile)) {
    die("Le fichier gestion.sql est introuvable après extraction.\n");
}

// Création de config.php
echo "Création du fichier config.php...\n";
$config = "<?php
define('DB_HOST', '$host');
define('DB_USER', '$user');
define('DB_PASS', '$pass');
define('DB_NAME', '$dbname');

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
echo "Connexion à MySQL et importation du fichier SQL...\n";
$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_errno) {
    die("Erreur de connexion MySQL : " . $mysqli->connect_error . "\n");
}
$mysqli->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci");
$mysqli->select_db($dbname);
$sql = file_get_contents($sqlFile);
if (!$sql) {
    die("Erreur lors de la lecture du fichier SQL.\n");
}
if (!$mysqli->multi_query($sql)) {
    die("Erreur lors de l'import SQL : " . $mysqli->error . "\n");
}

echo "✅ Installation terminée avec succès.\n";
