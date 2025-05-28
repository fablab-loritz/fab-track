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
require_once 'config.php';
$pdo = getPDO();
// 1. Initialiser tous les totaux à zéro
$pdo->exec("UPDATE responsibles SET total = 0");

// 2. Récupérer le nombre d’entrées par responsable
$sql = "SELECT responsible_id, COUNT(*) AS total_entries FROM enregistrements GROUP BY responsible_id";
$stmt = $pdo->query($sql);

if (!$stmt) {
    die("Erreur dans la requête SQL pour compter les entrées.");
}

$totals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Mettre à jour les totaux pour les responsables avec des entrées
foreach ($totals as $row) {
    $responsible_id = (int)$row['responsible_id'];
    $total_entries = (int)$row['total_entries'];

    $updateStmt = $pdo->prepare("UPDATE responsibles SET total = ? WHERE id = ?");
    $updateStmt->execute([$total_entries, $responsible_id]);
}
// Démarrage du buffer de sortie
ob_start();

// recuperations des entrées dans Responsable
$sql = "SELECT responsible_id, COUNT(*) AS total_entries FROM enregistrements GROUP BY responsible_id";
$stmt = $pdo->query($sql);

if (!$stmt) {
    die("Erreur dans la requête SQL pour compter les entrées.");
}

$totals = $stmt->fetchAll(PDO::FETCH_ASSOC);

//mise a jour 
foreach ($totals as $row) {
    $responsible_id = (int)$row['responsible_id'];
    $total_entries = (int)$row['total_entries'];

    $updateStmt = $pdo->prepare("UPDATE responsibles SET total = ? WHERE id = ?");
    $updateStmt->execute([$total_entries, $responsible_id]);
}
$responsablesStmt = $pdo->query("SELECT id, name, total FROM responsibles ORDER BY name ASC");
$responsables = $responsablesStmt->fetchAll(PDO::FETCH_ASSOC);

echo '<div class="responsibles-box">';
echo '<strong>Responsables et Entrées</strong>';
echo '<table>';
echo '<tr><th>Responsable</th><th>Entrées</th></tr>';

foreach ($responsables as $r) {
    echo '<tr>';
    echo '<td>' . htmlentities($r['name']) . '</td>';
    echo '<td style="text-align:center;">' . (int)$r['total'] . '</td>';
    echo '</tr>';
}

echo '</table>';
echo '</div>';

// Affichage du titre
echo '<h4>Fab-Track</h4>';

// --- GESTION DE LA SUPPRESSION ---
if (isset($_POST['delete'])) {
    $id_to_delete = intval($_POST['delete']);
    $stmt = $pdo->prepare("DELETE FROM enregistrements WHERE id = ?");
    $stmt->execute([$id_to_delete]);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Récupération des machines
$stmt = $pdo->query("SELECT ID, NAME FROM machines");
$machinesList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Affichage du select machines
echo '<form method="post">';
echo '<select name="machines" onchange="this.form.submit()">';
echo '<option value="" disabled selected>Sélectionner une machine</option>';
foreach ($machinesList as $machine) {
    $selected = (isset($_POST['machines']) && $_POST['machines'] == $machine['ID']) ? 'selected' : '';
    echo '<option value="' . $machine['ID'] . '" ' . $selected . '>' . htmlentities($machine['NAME']) . '</option>';
}
echo '</select>';
echo '</form>';

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['delete']) && !empty($_POST['machines'])) {
    $machine_id = intval($_POST['machines']);

    $stmt = $pdo->prepare("SELECT category FROM machines WHERE ID = ?");
    $stmt->execute([$machine_id]);
    $category_name = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT id FROM material_types WHERE name = ?");
    $stmt->execute([$category_name]);
    $category_id = $stmt->fetchColumn();

    echo '<form method="post">';
    echo '<input type="hidden" name="machines" value="' . htmlentities($machine_id) . '">';

    $stmt = $pdo->prepare("SELECT id, name FROM modele WHERE machine_id = ?");
    $stmt->execute([$machine_id]);
    $modeles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($modeles) {
        echo '<h2>Liste des modèles :</h2>';
        echo '<select name="modele" required>';
        echo '<option value="" disabled selected>Sélectionner un modèle</option>';
        foreach ($modeles as $modele) {
            echo '<option value="' . $modele['id'] . '">' . htmlentities($modele['name']) . '</option>';
        }
        echo '</select>';
    } else {
        echo "<p>Aucun modèle disponible.</p>";
        echo '<input type="hidden" name="modele" value="">';
    }

    $stmt = $pdo->prepare("SELECT id, name, unit FROM materials WHERE material_type_id = ?");
    $stmt->execute([$category_id]);
    $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $epaisseurs_par_material = [
        'mdf' => [4, 6, 8],
        'plexy' => [4, 6],
        'carton' => [3, 6, 8],
        'mousse' => [3, 6]
    ];

    if ($materials) {
        echo '<h2>Matériau :</h2>';
        echo '<select name="material" required>';
        echo '<option value="" disabled selected>Sélectionner un matériau</option>';
        foreach ($materials as $mat) {
            $epaisseurs = $epaisseurs_par_material[strtolower($mat['name'])] ?? [];
            if ($epaisseurs) {
                foreach ($epaisseurs as $ep) {
                    echo '<option value="' . $mat['id'] . '_' . $ep . '">' . htmlentities($mat['name'] . " {$ep}mm ({$mat['unit']})") . '</option>';
                }
            } else {
                echo '<option value="' . $mat['id'] . '">' . htmlentities($mat['name'] . " ({$mat['unit']})") . '</option>';
            }
        }
        echo '</select>';
    } else {
        echo "<p>Aucun matériau disponible.</p>";
    }

    echo '<h2>Professeur référent :</h2>';
    $stmt = $pdo->query("SELECT first_name, last_name FROM professors");

    echo '<input list="professors" name="professor_name" required>';
    echo '<datalist id="professors">';
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $p) {
        $fullname = htmlentities($p['first_name'] . ' ' . $p['last_name']);
        echo '<option value="' . $fullname . '">';
    }
    echo '</datalist>';

    echo '<h2>Classe :</h2>';
    $stmt = $pdo->query("SELECT id, name FROM classes");
    echo '<select name="class_id" required>';
    echo '<option value="" disabled selected>Sélectionner</option>';
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
        echo '<option value="' . $c['id'] . '">' . htmlentities($c['name']) . '</option>';
    }
    echo '</select>';

    echo '<h2>Responsable :</h2>';
    $stmt = $pdo->query("SELECT id, name FROM responsibles");
    echo '<select name="responsible_id" required>';
    echo '<option value="" disabled selected>Sélectionner</option>';
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        echo '<option value="' . $r['id'] . '">' . htmlentities($r['name']) . '</option>';
    }
    echo '</select>';

    echo '<h2>Quantité :</h2>';
    echo '<input type="number" name="quantite" min="1" required>';


    echo '<br><br><button type="submit">Valider</button>';
    echo '</form>';

    // Traitement du formulaire
    if (
        !empty($_POST['material']) &&
        isset($_POST['quantite'], $_POST['professor_name'], $_POST['class_id'], $_POST['responsible_id']) &&
        (isset($_POST['modele']) || count($modeles) === 0)
    ) {
        $material_post = $_POST['material'];
        $epaisseur = null;

        if (strpos($material_post, '_') !== false) {
            [$material_id, $epaisseur] = explode('_', $material_post);
            $material_id = intval($material_id);
            $epaisseur = intval($epaisseur);
        } else {
            $material_id = intval($material_post);
        }

        $modele_id = $_POST['modele'] !== '' ? intval($_POST['modele']) : null;
        $quantite = intval($_POST['quantite']);
        $class_id = intval($_POST['class_id']);
        $responsible_id = intval($_POST['responsible_id']);
        $professor_id = null;

        if (!empty($_POST['professor_name'])) {
            $prof_name = trim($_POST['professor_name']);
            $parts = explode(' ', $prof_name, 2);
            if (count($parts) === 2) {
                $stmt = $pdo->prepare("SELECT id FROM professors WHERE first_name = ? AND last_name = ?");
                $stmt->execute([$parts[0], $parts[1]]);
                $professor_id = $stmt->fetchColumn();
            }
        }

        if (!$professor_id) {
            echo "<p style='color:red;'>Erreur : professeur non reconnu.</p>";
            // On ne redirige pas pour permettre à l'utilisateur de corriger
        } else {
            $sql = "INSERT INTO enregistrements (
                        machine_id, material_id, quantite, date_enregistrement,
                        professor_id, class_id, responsible_id";
            $params = [$machine_id, $material_id, $quantite, $professor_id, $class_id, $responsible_id];

            if ($modele_id !== null) {
                $sql .= ", modele_id";
                $params[] = $modele_id;
            }

            if ($epaisseur !== null) {
                $sql .= ", epaisseur";
                $params[] = $epaisseur;
            }

            $sql .= ") VALUES (?, ?, ?, NOW(), ?, ?, ?";

            if ($modele_id !== null) $sql .= ", ?";
            if ($epaisseur !== null) $sql .= ", ?";
            $sql .= ")";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            // Redirection après insertion pour éviter le repost du formulaire
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
}

// Historique des enregistrements
if (isset($_POST['machines']) && is_numeric($_POST['machines'])) {
    $machine_id = intval($_POST['machines']);

    $stmt = $pdo->prepare("SELECT 
            e.id,
            m.name AS machine,
            mo.name AS modele,
            CONCAT(ma.name, IFNULL(CONCAT(' - ', v.description), '')) AS materiau,
            e.epaisseur,
            e.quantite,
            e.date_enregistrement,
            CONCAT(p.first_name, ' ', p.last_name) AS professeur,
            c.name AS classe,
            r.name AS preparateur
        FROM enregistrements e
        JOIN machines m ON e.machine_id = m.id
        LEFT JOIN modele mo ON e.modele_id = mo.id
        JOIN materials ma ON e.material_id = ma.id
        LEFT JOIN variantes v ON e.variantes = v.id
        LEFT JOIN professors p ON e.professor_id = p.id
        LEFT JOIN classes c ON e.class_id = c.id
        LEFT JOIN responsibles r ON e.responsible_id = r.id
        WHERE e.machine_id = ?
        ORDER BY e.date_enregistrement DESC");
    $stmt->execute([$machine_id]);
} else {
    $stmt = $pdo->query("SELECT 
            e.id,
            m.name AS machine,
            mo.name AS modele,
            CONCAT(ma.name, IFNULL(CONCAT(' - ', v.description), '')) AS materiau,
            e.epaisseur,
            e.quantite,
            e.date_enregistrement,
            CONCAT(p.first_name, ' ', p.last_name) AS professeur,
            c.name AS classe,
            r.name AS preparateur
        FROM enregistrements e
        JOIN machines m ON e.machine_id = m.id
        LEFT JOIN modele mo ON e.modele_id = mo.id
        JOIN materials ma ON e.material_id = ma.id
        LEFT JOIN variantes v ON e.variantes = v.id
        LEFT JOIN professors p ON e.professor_id = p.id
        LEFT JOIN classes c ON e.class_id = c.id
        LEFT JOIN responsibles r ON e.responsible_id = r.id
        ORDER BY e.date_enregistrement DESC");
}
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tableau du bas
echo '<div style="max-height:400px; overflow:auto; margin-top:50px;">';
echo '<table border="1" cellspacing="0" cellpadding="3">';
echo '<tr>
    <th>Machine</th><th>Modèle</th><th>Matériau</th><th>Quantité</th><th>Date</th><th>Professeur</th><th>Classe</th><th>Responsable</th><th>Supprimer</th>
</tr>';

foreach ($entries as $entry) {
    echo '<tr>';
    echo '<td>' . htmlentities($entry['machine']) . '</td>';
    echo '<td>' . htmlentities($entry['modele'] ?? '') . '</td>';
    echo '<td>' . htmlentities($entry['materiau']) . '</td>';
    echo '<td style="text-align:center;">' . (int)$entry['quantite'] . '</td>';
    echo '<td>' . htmlentities($entry['date_enregistrement']) . '</td>';
    echo '<td>' . htmlentities($entry['professeur'] ?? '') . '</td>';
    echo '<td>' . htmlentities($entry['classe'] ?? '') . '</td>';
    echo '<td>' . htmlentities($entry['preparateur'] ?? '') . '</td>';
    echo '<td><form method="post" onsubmit="return confirm(\'Confirmer la suppression ?\');">
            <button type="submit" name="delete" value="' . (int)$entry['id'] . '">🥸</button>
          </form></td>';
    echo '</tr>';
}
echo '</table>';
echo '</div>';

// Fin du buffer et envoi du contenu
ob_end_flush();
?>

</div>
</body>
</html>
