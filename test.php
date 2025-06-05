<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>gestion</title>
    <link rel="stylesheet" href="TropBeau.css"/>
</head>
<body>
<div align="center">

<?php
ob_start();
require_once 'config.php';
$pdo = getPDO();

// --- Mise à jour des totaux dans la table responsibles ---
$pdo->exec("UPDATE responsibles SET total = 0");

$sql = "SELECT responsible_id, COUNT(*) AS total_entries FROM enregistrements GROUP BY responsible_id";
$stmt = $pdo->query($sql);
$totals = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($totals as $row) {
    $updateStmt = $pdo->prepare("UPDATE responsibles SET total = ? WHERE id = ?");
    $updateStmt->execute([(int)$row['total_entries'], (int)$row['responsible_id']]);
}

// --- Affichage des responsables et leurs totaux ---
$responsablesStmt = $pdo->query("SELECT id, name, total FROM responsibles ORDER BY name ASC");
$responsables = $responsablesStmt->fetchAll(PDO::FETCH_ASSOC);

echo '<div class="responsibles-box">';
echo '<strong>Responsables et Entrées</strong>';
echo '<table>';
echo '<tr><th>Responsable</th><th>Entrées</th></tr>';
foreach ($responsables as $r) {
    echo '<tr><td>' . $r['name'] . '</td><td style="text-align:center;">' . (int)$r['total'] . '</td></tr>';
}
echo '</table>';
echo '</div>';

echo '<h4>Fab-Track</h4>';

// --- Suppression multiple ---
if (isset($_POST['delete_selected']) && !empty($_POST['delete_ids'])) {
    $ids_to_delete = array_map('intval', $_POST['delete_ids']);
    if ($ids_to_delete) {
        $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));
        $stmt = $pdo->prepare("DELETE FROM enregistrements WHERE id IN ($placeholders)");
        $stmt->execute($ids_to_delete);
    }
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// --- Sélection de la machine ---
$stmt = $pdo->query("SELECT ID, NAME FROM machines");
$machinesList = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<form method="post">';
echo '<select name="machines" onchange="this.form.submit()">';
echo '<option value="" disabled selected>Sélectionner une machine</option>';
foreach ($machinesList as $machine) {
    $selected = (isset($_POST['machines']) && $_POST['machines'] == $machine['ID']) ? 'selected' : '';
    echo '<option value="' . $machine['ID'] . '" ' . $selected . '>' . $machine['NAME'] . '</option>';
}
echo '</select>';
echo '</form>';

// --- Affichage formulaire selon la machine ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['delete_selected']) && !empty($_POST['machines'])) {
    $machine_id = intval($_POST['machines']);

    // Récupération catégorie machine
    $stmt = $pdo->prepare("SELECT category FROM machines WHERE ID = ?");
    $stmt->execute([$machine_id]);
    $category_name = $stmt->fetchColumn();

    // Récupération id catégorie matériau
    $stmt = $pdo->prepare("SELECT id FROM material_types WHERE name = ?");
    $stmt->execute([$category_name]);
    $category_id = $stmt->fetchColumn();

    echo '<form method="post">';
    echo '<input type="hidden" name="machines" value="' . $machine_id . '">';

    // Modèles
    $stmt = $pdo->prepare("SELECT id, name FROM modele WHERE machine_id = ?");
    $stmt->execute([$machine_id]);
    $modeles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($modeles) {
        echo '<h2>Liste des modèles :</h2>';
        echo '<select name="modele" required>';
        echo '<option value="" disabled selected>Sélectionner un modèle</option>';
        foreach ($modeles as $modele) {
            echo '<option value="' . $modele['id'] . '">' . $modele['name'] . '</option>';
        }
        echo '</select>';
    } else {
        echo "<p>Aucun modèle disponible.</p>";
        echo '<input type="hidden" name="modele" value="">';
    }

    // Matériaux ou variantes
    if (strtolower($category_name) === 'papier') {
        $stmt = $pdo->query("SELECT id, description FROM variantes");
        $variantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo '<h2>Variante :</h2>';
        echo '<select name="material" required>';
        echo '<option value="" disabled selected>Sélectionner une variante</option>';
        foreach ($variantes as $v) {
            echo '<option value="' . $v['id'] . '">' . $v['description'] . '</option>';
        }
        echo '</select>';
    } else {
        $stmt = $pdo->prepare("SELECT id, name, unit FROM materials WHERE material_type_id = ?");
        $stmt->execute([$category_id]);
        $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $epaisseurs_par_material = [
            'mdf' => [4, 6, 8],
            'plexy' => [4, 6],
            'carton' => [3, 6, 8],
            'mousse' => [3, 6]
        ];

        echo '<h2>Matériau :</h2>';
        echo '<select name="material" required>';
        echo '<option value="" disabled selected>Sélectionner un matériau</option>';
        foreach ($materials as $mat) {
            $name_lower = strtolower($mat['name']);
            $epaisseurs = $epaisseurs_par_material[$name_lower] ?? [];
            if ($epaisseurs) {
                foreach ($epaisseurs as $ep) {
                    echo '<option value="' . $mat['id'] . '_' . $ep . '">' . $mat['name'] . " {$ep}mm ({$mat['unit']})" . '</option>';
                }
            } else {
                echo '<option value="' . $mat['id'] . '">' . $mat['name'] . " ({$mat['unit']})" . '</option>';
            }
        }
        echo '</select>';
    }

    // Professeur
    echo '<h2>Professeur référent :</h2>';
    $stmt = $pdo->query("SELECT id, first_name, last_name FROM professors ORDER BY last_name, first_name");
    $professors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<input list="professors_list" name="professor_name" placeholder="Rechercher un professeur" required autocomplete="off">';
    echo '<datalist id="professors_list">';
    foreach ($professors as $p) {
        $fullname = $p['first_name'] . ' ' . $p['last_name'];
        echo '<option value="' . $fullname . '">';
    }
    echo '</datalist>';

    // Classe
    echo '<h2>Classe :</h2>';
    $stmt = $pdo->query("SELECT id, name FROM classes");
    echo '<select name="class_id" required>';
    echo '<option value="" disabled selected>Sélectionner</option>';
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
        echo '<option value="' . $c['id'] . '">' . $c['name'] . '</option>';
    }
    echo '</select>';

    // Responsable
    echo '<h2>Responsable :</h2>';
    $stmt = $pdo->query("SELECT id, name FROM responsibles");
    echo '<select name="responsible_id" required>';
    echo '<option value="" disabled selected>Sélectionner</option>';
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        echo '<option value="' . $r['id'] . '">' . $r['name'] . '</option>';
    }
    echo '</select>';

    // Quantité
    echo '<h2>Quantité :</h2>';
    echo '<input type="number" name="quantite" min="1" required>';

    echo '<br><br><input type="submit" value="Enregistrer">';
    echo '</form>';
}

// --- Traitement enregistrement ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['machines']) && !isset($_POST['delete_selected'])) {
    $machine_id = intval($_POST['machines']);
    $modele_id = intval($_POST['modele'] ?? 0);

    $material_raw = $_POST['material'] ?? '';
    $material_id = 0;
    $epaisseur = null;
    if ($material_raw !== '') {
        if (strpos($material_raw, '_') !== false) {
            list($material_id_str, $epaisseur_str) = explode('_', $material_raw);
            $material_id = intval($material_id_str);
            $epaisseur = intval($epaisseur_str);
        } else {
            $material_id = intval($material_raw);
        }
    }

    $professor_name = trim($_POST['professor_name'] ?? '');
    $professor_id = 0;
    if ($professor_name !== '') {
        $stmt = $pdo->prepare("SELECT id FROM professors WHERE CONCAT(first_name, ' ', last_name) = ?");
        $stmt->execute([$professor_name]);
        $professor_id = (int)$stmt->fetchColumn();
    }

    $class_id = intval($_POST['class_id'] ?? 0);
    $responsible_id = intval($_POST['responsible_id'] ?? 0);
    $quantite = intval($_POST['quantite'] ?? 0);

    if ($machine_id <= 0 || $modele_id <= 0 || $material_id <= 0 || $professor_id <= 0 || $class_id <= 0 || $responsible_id <= 0 || $quantite <= 0) {
        echo "<p style='color:red;'>Veuillez remplir tous les champs obligatoires correctement.</p>";
    } else {
        $stockCheckStmt = $pdo->prepare("SELECT stock FROM materials WHERE id = ?");
        $stockCheckStmt->execute([$material_id]);
        $stock_quantity = $stockCheckStmt->fetchColumn();

        if ($stock_quantity === false) {
            echo "<p style='color:red;'>Erreur : matériel non trouvé dans les matériaux.</p>";
        } elseif ($stock_quantity < $quantite) {
            echo "<p style='color:red;'>Stock insuffisant. Disponible : $stock_quantity, demandé : $quantite.</p>";
        } else {
            $insertStmt = $pdo->prepare("INSERT INTO enregistrements 
                (machine_id, modele_id, material_id, date_enregistrement, quantite, epaisseur, professor_id, class_id, responsible_id) 
                VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?)");
            $insertStmt->execute([$machine_id, $modele_id, $material_id, $quantite, $epaisseur, $professor_id, $class_id, $responsible_id]);

            $updateStock = $pdo->prepare("UPDATE materials SET stock = stock - ? WHERE id = ?");
            $updateStock->execute([$quantite, $material_id]);

            echo "<p style='color:green;'>Enregistrement ajouté et stock mis à jour avec succès.</p>";
            header("Refresh:2");
        }
    }
}

// --- Affichage des enregistrements ---
echo "<h3>Enregistrements :</h3>";
$enregStmt = $pdo->query("SELECT e.id, m.NAME AS machine_name, mo.name AS modele_name, ma.name AS material_name, e.epaisseur, e.quantite, e.date_enregistrement, p.first_name, p.last_name, c.name AS class_name, r.name AS responsible_name 
FROM enregistrements e
LEFT JOIN machines m ON e.machine_id = m.ID
LEFT JOIN modele mo ON e.modele_id = mo.id
LEFT JOIN materials ma ON e.material_id = ma.id
LEFT JOIN professors p ON e.professor_id = p.id
LEFT JOIN classes c ON e.class_id = c.id
LEFT JOIN responsibles r ON e.responsible_id = r.id
ORDER BY e.date_enregistrement DESC");

$enregistrements = $enregStmt->fetchAll(PDO::FETCH_ASSOC);

echo '<form method="post">';
echo '<table border="1" cellpadding="5">';
echo '<tr><th>Supprimer</th><th>Machine</th><th>Modèle</th><th>Matériau</th><th>Épaisseur</th><th>Quantité</th><th>Date</th><th>Professeur</th><th>Classe</th><th>Responsable</th></tr>';

foreach ($enregistrements as $e) {
    echo '<tr>';
    echo '<td><input type="checkbox" name="delete_ids[]" value="' . $e['id'] . '"></td>';
    echo '<td>' . htmlspecialchars($e['machine_name']) . '</td>';
    echo '<td>' . htmlspecialchars($e['modele_name']) . '</td>';
    echo '<td>' . htmlspecialchars($e['material_name']) . '</td>';
    echo '<td>' . htmlspecialchars($e['epaisseur'] ?? '') . '</td>';
    echo '<td style="text-align:center;">' . (int)$e['quantite'] . '</td>';
    echo '<td>' . htmlspecialchars($e['date_enregistrement']) . '</td>';
    echo '<td>' . htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) . '</td>';
    echo '<td>' . htmlspecialchars($e['class_name']) . '</td>';
    echo '<td>' . htmlspecialchars($e['responsible_name']) . '</td>';
    echo '</tr>';
}

echo '</table>';
echo '<br><input type="submit" name="delete_selected" value="Supprimer sélection">';
echo '</form>';

ob_end_flush();
?>

</div>
</body>
</html>
