<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Page créée par Matheo Pereira -->
    <meta charset="utf-8">
    <title>gestion</title>
    <link rel="stylesheet" href="TropBeau.css"/>
</head>
<body>
    <div align="center">
        <h1>Fab-Track</h1>

        <?php
try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=gestion", "root", "");
} catch (PDOException $e) {
    exit('Erreur lors de la connexion : ' . $e->getMessage());
}

// --- GESTION DE LA SUPPRESSION ---
if (isset($_POST['delete'])) {
    $id_to_delete = intval($_POST['delete']);
    $stmt = $pdo->prepare("DELETE FROM enregistrements WHERE id = ?");
    $stmt->execute([$id_to_delete]);
    echo "<p>Ligne supprimée avec succès.</p>";
}

// Vérifier si une machine est sélectionnée
$machines = null;
if (!empty($_POST['machines']) && !isset($_POST['delete'])) {
    $id = intval($_POST['machines']);
    $stmt = $pdo->prepare("SELECT * FROM machines WHERE ID = ?");
    $stmt->execute([$id]);
    $machines = $stmt->fetch();
}

// Récupération des machines
$result = $pdo->query("SELECT ID, NAME, CATEGORY FROM machines");

echo '<form method="post">';
echo '<select name="machines" onchange="this.form.submit()">';
echo '<option value="" disabled selected>Sélectionner une machine</option>';

while ($row = $result->fetch()) {
    $selected = (isset($_POST['machines']) && $_POST['machines'] == $row['ID']) ? 'selected' : '';
    echo '<option value="' . $row['ID'] . '" ' . $selected . '>' . htmlentities($row['NAME']) . '</option>';
}

echo '</select>';
echo '</form>';

// Partie liste des matériaux et modèles
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete'])) {
    $machine_id = $_POST["machines"] ?? "";

    $conn = new mysqli("localhost", "root", "", "gestion");

    if ($conn->connect_error) {
        die("Connexion échouée : " . $conn->connect_error);
    }

    // Récupérer la catégorie de la machine
    $sql = "SELECT category FROM machines WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $machine_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $category_name = $row['category'] ?? null;
    $stmt->close();

    // Récupérer l'ID correspondant dans `material_types`
    $category_id = null;
    if ($category_name) {
        $sql = "SELECT id FROM material_types WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $category_id = $row['id'] ?? null;
        $stmt->close();
    }

    // Début du formulaire pour modèle + matériau + quantité
    echo '<form method="post">';
    echo '<input type="hidden" name="machines" value="' . htmlentities($machine_id) . '">';

    // Liste des modèles
    if (!empty($machine_id)) {
        $sql = "SELECT id, name FROM modele WHERE machine_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $machine_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<h2>Liste des modèles :</h2>';
            echo '<select name="modele" required>';
            echo '<option value="" disabled selected>Sélectionner un modèle</option>';

            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['id'] . '">' . htmlentities($row['name']) . '</option>';
            }

            echo '</select>';
        } else {
            echo "<p>Aucun modèle disponible pour cette machine.</p>";
        }

        $stmt->close();
    }

    // Liste des matériaux
    if ($category_id) {
        $sql = "SELECT id, name, unit FROM materials WHERE material_type_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo "<p>Aucun matériau trouvé pour cette catégorie.</p>";
        } else {
            echo '<h2>Choisissez un matériau :</h2>';
            echo '<select name="material" required>';
            echo '<option value="" disabled selected>Sélectionner un matériau</option>';

            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['id'] . '">' . htmlentities($row['name']) . ' (' . htmlentities($row['unit']) . ')</option>';
            }

            echo '</select>';
        }

        $stmt->close();
    } else {
        echo "<p>Aucune catégorie trouvée pour cette machine.</p>";
    }

    // Champ quantité
    echo '<br><br><label>Quantité :</label> <input type="number" name="quantite" min="1" required>';

    // Bouton de soumission
    echo '<br><br><button type="submit">Valider</button>';
    echo '</form>';

    // Insertion dans la base si tout est rempli
    if (!empty($_POST['material']) && !empty($_POST['modele']) && !empty($_POST['quantite'])) {
        $material_id = $_POST['material'];
        $modele_id = $_POST['modele'];
        $quantite = $_POST['quantite'];

        $stmt = $conn->prepare("INSERT INTO enregistrements (machine_id, modele_id, material_id, quantite, date_enregistrement) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiii", $machine_id, $modele_id, $material_id, $quantite);
        $stmt->execute();
        $stmt->close();
        echo "<p>Données enregistrées avec succès !</p>";
    }

    $conn->close();
}

// --- AFFICHAGE HISTORIQUE AVEC BOUTONS SUPPRIMER ---
$stmt = $pdo->query("
    SELECT 
        e.id,
        m.NAME AS machine,
        mo.name AS modele,
        ma.name AS materiau,
        e.quantite,
        e.date_enregistrement
    FROM enregistrements e
    JOIN machines m ON e.machine_id = m.ID
    JOIN modele mo ON e.modele_id = mo.id
    JOIN materials ma ON e.material_id = ma.id
    ORDER BY e.date_enregistrement DESC
");

echo '<h2>Historique des enregistrements :</h2>';
echo '<form method="post">';
echo '<table border="1" cellpadding="5">';
echo '<tr><th>Action</th><th>Machine</th><th>Modèle</th><th>Matériau</th><th>Quantité</th><th>Date</th></tr>';

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr>';
    echo '<td><button type="submit" name="delete" value="' . $row['id'] . '" onclick="return confirm(\'Supprimer cette ligne ?\')">🗑️</button></td>';
    echo '<td>' . htmlentities($row['machine']) . '</td>';
    echo '<td>' . htmlentities($row['modele']) . '</td>';
    echo '<td>' . htmlentities($row['materiau']) . '</td>';
    echo '<td>' . $row['quantite'] . '</td>';
    echo '<td>' . $row['date_enregistrement'] . '</td>';
    echo '</tr>';
}
echo '</table>';
echo '</form>';
?>
</div>
</body>
</html>
