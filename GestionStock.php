<?php
include 'config.php';

// Gestion du mode sombre via cookie
if (isset($_GET['darkmode'])) {
    setcookie('darkmode', $_GET['darkmode'], time() + 365*24*3600, "/");
    $_COOKIE['darkmode'] = $_GET['darkmode']; // Pour effet immédiat
}
$darkmode = (!empty($_COOKIE['darkmode']) && $_COOKIE['darkmode'] === 'on');

try {
    $bdd = getPDO();
    $bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

// Mise à jour du stock si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    foreach ($_POST['stock'] as $id => $value) {
        $stmt = $bdd->prepare("UPDATE materials SET stock = ? WHERE id = ?");
        $stmt->execute([intval($value), intval($id)]);
    }
}

// Récupération des matériaux avec la colonne image
$sql = "SELECT SQL_NO_CACHE id, name, unit, stock, image FROM materials";
$stmt = $bdd->query($sql);
$materials = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Gestion du Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/custom.css">
</head>
<link rel="icon" type="image/x-icon" href="icones/logo-fab-track.ico">
<body
<?php
    $classes = [];
    if ($darkmode) $classes[] = 'dark-mode';
    if ($classes) echo ' class="' . implode(' ', $classes) . '"';
?>>
<nav class="navbar navbar-light bg-white shadow-sm mb-4">
  <div class="container-fluid">
    <?php
    $toggleUrl = $_SERVER['PHP_SELF'] . '?darkmode=' . ($darkmode ? 'off' : 'on');
    ?>
    <a href="<?= htmlspecialchars($toggleUrl) ?>" class="btn btn-outline-primary me-2">
        <?= $darkmode ? 'Mode clair' : 'Mode sombre' ?>
    </a>
    <a href="ConsulterTableau.php" class="btn btn-outline-secondary me-2">Consulter Tableau</a>
    <a href="Fab-Track.php" class="btn btn-outline-secondary me-2">FabTrack</a>
    <a href="admin.php" class="btn btn-outline-secondary me-2">Admin</a>
    <span class="navbar-brand ms-auto">Gestion du Stock</span>
  </div>
</nav>
<div class="container">
    <h4 class="mb-4 text-primary fw-bold text-center">Gestion du Stock</h4>
    <form method="post">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Unité</th>
                    <th>Stock</th>
                    <th>Modifier</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materials as $mat): ?>
                <tr>
                    <td>
                        <img src="icones/<?= htmlentities($mat['image'] ?: 'default.png') ?>" alt="" width="40">
                    </td>
                    <td><?= htmlentities($mat['name']) ?></td>
                    <td><?= htmlentities($mat['unit']) ?></td>
                    <td><?= (int)$mat['stock'] ?></td>
                    <td>
                        <input type="number" name="stock[<?= $mat['id'] ?>]" value="<?= (int)$mat['stock'] ?>" class="form-control" style="width:100px;">
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" name="update_stock" class="btn btn-success">Mettre à jour</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>