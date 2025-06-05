<?php
include 'dbc.php';

try {
    $bdd = new PDO("mysql:host=$host_bdd;port=$port_bdd;dbname=$base_bdd", $user_bdd, $pass_bdd);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

// Mise à jour du stock si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $id = $_POST['id'];
    $new_stock = $_POST['stock'];

    $update = $bdd->prepare("UPDATE materials SET stock = :stock WHERE id = :id");
    $update->execute([
        ':stock' => $new_stock,
        ':id' => $id
    ]);

    // Redirection pour éviter le repost et forcer rechargement des données
    header('Location: ' . $_SERVER['PHP_SELF'] . '?refresh=' . time());
    exit;
}

// Récupération des matériaux avec SQL_NO_CACHE pour éviter cache MySQL
$sql = "SELECT SQL_NO_CACHE id, name, unit, stock FROM materials";
$stmt = $bdd->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestion du Stock</title>
    <link rel="stylesheet" href="TropBeau.css">
</head>
<body>
    <h2>Gestion du Stock</h2>
    <table>
        <tr>
            <th>Nom</th>
            <th>Unité</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $stmt->fetch()) : ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['unit']) ?></td>
            <td>
                <form method="POST">
                    <input type="number" name="stock" value="<?= htmlspecialchars($row['stock']) ?>" required>
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
            </td>
            <td>
                    <button type="submit" name="update_stock">Mettre à jour</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
