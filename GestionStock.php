<?php
include 'dbc.php';

try {
    $bdd = new PDO("mysql:host=$host_bdd;port=$port_bdd;dbname=$base_bdd", $user_bdd, $pass_bdd);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

// Gestion du mode sombre via cookie
if (isset($_GET['darkmode'])) {
    setcookie('darkmode', $_GET['darkmode'], time() + 365*24*3600, "/");
    $_COOKIE['darkmode'] = $_GET['darkmode']; // Pour effet immédiat
}
$darkmode = (!empty($_COOKIE['darkmode']) && $_COOKIE['darkmode'] === 'on');

// Mise à jour du stock si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $id = $_POST['id'];
    $new_stock = $_POST['stock'];

    $update = $bdd->prepare("UPDATE materials SET stock = :stock WHERE id = :id");
    $update->execute([
        ':stock' => $new_stock,
        ':id' => $id
    ]);

    header('Location: ' . $_SERVER['PHP_SELF'] . '?refresh=' . time());
    exit;
}

$sql = "SELECT SQL_NO_CACHE id, name, unit, stock FROM materials";
$stmt = $bdd->query($sql);

// Fonction pour trouver le fichier image correspondant à un nom de matériau

function getMaterialImage($name) {
    $imgDir = 'images/';
    $map = [
        'Polystyrène extrudé' => 'PolystyreneExtrude.jpg',
        'PLA' => 'pla.png',
        'ABS' => 'abs.png',
        'PETG' => 'petg.png',
        'MDF' => 'mdf.jpg',
        'Plexy' => 'plexi.png',
        'Carton' => 'carton.avif',
        'Mousse' => 'mousse.jpg',
        'Alu' => 'alu.jpg',
        'Papier A4' => 'a4couleur.jpg',
        'Papier A4 noir et blanc' => 'A4.jpg',
        'Papier A3' => 'a3couleur.jpg',
        'Papier A3 noir et blanc' => 'A3.png',
        'Papier A2' => 'a2couleur.jpg',
        'Papier A2 noir et blanc' => 'A2.webp',
        'Papier A1' => 'a1_couleur.jpg',
        'Papier A1 noir et blanc' => 'A1.webp',
    ];
    
    // Normaliser la casse et les espaces pour correspondre plus facilement
    $normalized_name = strtolower(trim($name));
    foreach ($map as $key => $filename) {
        if (strtolower($key) === $normalized_name && file_exists($imgDir . $filename)) {
            return $imgDir . $filename;
        }
    }
    
    return null;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion du Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/custom.css">
    <link rel="stylesheet" href="C:/Users/mat88/Downloads/virtualhost/css/custom.css" />
</head>
<body<?php if ($darkmode) echo ' class="dark-mode"'; ?>>
    <div class="container mt-5">
        <h2 class="mb-4 text-primary fw-bold">Gestion du Stock</h2>
        <div class="d-flex justify-content-end mb-2">
            <?php
                $toggleUrl = $_SERVER['PHP_SELF'] . '?darkmode=' . ($darkmode ? 'off' : 'on');
                $toggleLabel = $darkmode ? 'Mode normal' : 'Mode sombre';
                $toggleClass = $darkmode ? 'btn-warning' : 'btn-danger';
            ?>
            <a href="<?= htmlspecialchars($toggleUrl) ?>" class="btn <?= $toggleClass ?> fw-semibold">
                <?= $toggleLabel ?>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Unité</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $stmt->fetch()) : ?>
                    <tr>
                        <td>
                            <?php
                                $img = getMaterialImage($row['name']);
                                if ($img) {
                                    echo '<img src="' . htmlspecialchars($img) . '" alt="" style="height:32px;vertical-align:middle;margin-right:8px;">';
                                }
                                echo htmlspecialchars($row['name']);
                            ?>
                        </td>
                        <td><?= htmlspecialchars($row['unit']) ?></td>
                        <td>
                            <form method="POST" class="d-flex align-items-center mb-0">
                                <input type="number" name="stock" value="<?= htmlspecialchars($row['stock']) ?>" required class="form-control me-2" style="max-width:120px;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        </td>
                        <td>
                                <button type="submit" name="update_stock" class="btn btn-primary">Mettre à jour</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>