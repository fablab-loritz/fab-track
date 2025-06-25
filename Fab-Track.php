<?php
session_start();

// Gestion du mode sombre via cookie
if (isset($_GET['darkmode'])) {
    setcookie('darkmode', $_GET['darkmode'], time() + 365*24*3600, "/");
    $_COOKIE['darkmode'] = $_GET['darkmode']; // Pour effet immédiat
}
$darkmode = (!empty($_COOKIE['darkmode']) && $_COOKIE['darkmode'] === 'on');

require_once 'config.php';
$pdo = getPDO();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8" />
<title>Fab-Track - Gestion</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="css/custom.css">
<link rel="stylesheet" href="C:/Users/mat88/Downloads/virtualhost/css/custom.css" />
</head>
<link rel="icon" type="image/x-icon" href="icones/logo-fab-track.ico">
<body<?php
    $classes = [];
    if ($darkmode) $classes[] = 'dark-mode';
    if ($classes) echo ' class="' . implode(' ', $classes) . '"';
?>>

<nav class="navbar navbar-light bg-white shadow-sm mb-4">
  <div class="container-fluid d-flex align-items-center">
    <?php
      $toggleUrl = $_SERVER['PHP_SELF'] . '?darkmode=' . ($darkmode ? 'off' : 'on');
    ?>
    <a href="<?= htmlspecialchars($toggleUrl) ?>" class="btn btn-outline-primary me-2">
        <?= $darkmode ? 'Mode clair' : 'Mode sombre' ?>
    </a>
    <a href="ConsulterTableau.php" class="btn btn-outline-secondary me-2">Consulter Tableau</a>
    <a href="GestionStock.php" class="btn btn-outline-secondary me-2">Gestion Stock</a>
    <a href="admin.php" class="btn btn-outline-secondary me-2">Admin</a>
    <span class="navbar-brand ms-auto fw-bold d-flex align-items-center">
        Fab-Track
        <div class="logo-fabtrack-float ms-2">
            <img src="icones/logo-fab-track.ico" alt="Logo Fab-Track" class="logo-light">
            <img src="icones/logo-fab-track-Sombre.ico" alt="Logo Fab-Track sombre" class="logo-dark">
        </div>
    </span>
  </div>
</nav>

<div class="container">

<?php
ob_start();
$seuil = 5; // seuil d'alerte à adapter
$stmtAlerte = $pdo->prepare("SELECT name, stock FROM materials WHERE stock <= ?");
$stmtAlerte->execute([$seuil]);
$materiaux_alertes = $stmtAlerte->fetchAll(PDO::FETCH_ASSOC);

if ($materiaux_alertes) {
    echo "<div class='alert alert-warning alert-stock'><strong>Attention : stocks bas !</strong><ul>";
    foreach ($materiaux_alertes as $mat) {
        echo "<li>" . htmlspecialchars($mat['name']) . " (stock : " . (int)$mat['stock'] . ")</li>";
    }
    echo "</ul></div>";
}

// --- Mise à jour des totaux des responsables ---
$pdo->exec("UPDATE responsibles SET total = 0");
$sqlTotals = "SELECT responsible_id, COUNT(*) AS total_entries FROM enregistrements GROUP BY responsible_id";
$stmtTotals = $pdo->query($sqlTotals);
$totaux = $stmtTotals->fetchAll(PDO::FETCH_ASSOC);

foreach ($totaux as $t) {
    $update = $pdo->prepare("UPDATE responsibles SET total = ? WHERE id = ?");
    $update->execute([(int)$t['total_entries'], (int)$t['responsible_id']]);
}

// --- Affichage responsables ---
$responsablesStmt = $pdo->query("SELECT id, name, total FROM responsibles ORDER BY name ASC");
$responsables = $responsablesStmt->fetchAll(PDO::FETCH_ASSOC);

echo '<div id="table-responsables" class="mb-5">';
echo '<h5 class="text-primary fw-semibold">Responsables et Entrées</h5>';
echo '<table class="table table-striped table-bordered text-center align-middle">';
echo '<thead class="table-light"><tr><th>Responsable</th><th>Entrées</th></tr></thead><tbody>';
foreach ($responsables as $r) {
    echo '<tr><td>' . htmlspecialchars($r['name']) . '</td><td>' . (int)$r['total'] . '</td></tr>';
}
echo '</tbody></table>';
echo '</div>';

// --- Suppression multiple ---
if (isset($_POST['delete_selected']) && !empty($_POST['delete_ids'])) {
    $idsSuppr = array_map('intval', $_POST['delete_ids']);
    if ($idsSuppr) {
        $placeholders = implode(',', array_fill(0, count($idsSuppr), '?'));
        $stmtDelete = $pdo->prepare("DELETE FROM enregistrements WHERE id IN ($placeholders)");
        $stmtDelete->execute($idsSuppr);
    }
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// --- Liste machines ---
$stmtMachines = $pdo->query("SELECT ID, NAME FROM machines");
$machinesList = $stmtMachines->fetchAll(PDO::FETCH_ASSOC);

echo '<form method="post" class="mb-4">';
echo '<label for="machines" class="form-label fw-semibold">Sélectionner une machine 🎰 :</label>';
echo '<select id="machines" name="machines" class="form-select" onchange="this.form.submit()">';
echo '<option value="" disabled selected>Choisir...</option>';
foreach ($machinesList as $machine) {
    $selected = (isset($_POST['machines']) && $_POST['machines'] == $machine['ID']) ? 'selected' : '';
    echo '<option value="' . $machine['ID'] . '" ' . $selected . '>' . htmlspecialchars($machine['NAME']) . '</option>';
}
echo '</select>';
echo '</form>';

// --- Formulaire conditionnel ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['delete_selected']) && !empty($_POST['machines'])) {
    $machine_id = intval($_POST['machines']);

    // Récupérer la catégorie de la machine
    $stmtCat = $pdo->prepare("SELECT category FROM machines WHERE ID = ?");
    $stmtCat->execute([$machine_id]);
    $category_name = $stmtCat->fetchColumn();

    // Récupérer id catégorie dans material_types
    $stmtCatId = $pdo->prepare("SELECT id FROM material_types WHERE name = ?");
    $stmtCatId->execute([$category_name]);
    $category_id = $stmtCatId->fetchColumn();

    echo '<form method="post" class="border p-4 rounded shadow-sm bg-white">';
    echo '<input type="hidden" name="machines" value="' . $machine_id . '">';

    // Modèles
    $stmtModeles = $pdo->prepare("SELECT id, name FROM modele WHERE machine_id = ?");
    $stmtModeles->execute([$machine_id]);
    $modeles = $stmtModeles->fetchAll(PDO::FETCH_ASSOC);

    if ($modeles) {
        echo '<div class="mb-3">';
        echo '<label for="modele" class="form-label">Modèle 🤠 :</label>';
        echo '<select id="modele" name="modele" class="form-select" required>';
        echo '<option value="" disabled selected>Choisir un modèle</option>';
        foreach ($modeles as $modele) {
            echo '<option value="' . $modele['id'] . '">' . htmlspecialchars($modele['name']) . '</option>';
        }
        echo '</select></div>';
    } else {
        echo "<p class='text-warning'>Aucun modèle disponible.</p>";
        echo '<input type="hidden" name="modele" value="">';
    }

    // Matériaux ou variantes
    if (strtolower($category_name) === 'papier') {
        $stmtVar = $pdo->query("SELECT id, description FROM variantes");
        $variantes = $stmtVar->fetchAll(PDO::FETCH_ASSOC);

        echo '<div class="mb-3">';
        echo '<label for="material" class="form-label">Variante :</label>';
        echo '<select id="material" name="material" class="form-select" required>';
        echo '<option value="" disabled selected>Choisir une variante</option>';
        foreach ($variantes as $v) {
            echo '<option value="' . $v['id'] . '">' . htmlspecialchars($v['description']) . '</option>';
        }
        echo '</select></div>';

    } else {
        $stmtMat = $pdo->prepare("SELECT id, name, unit, stock FROM materials WHERE material_type_id = ?");
        $stmtMat->execute([$category_id]);
        $materials = $stmtMat->fetchAll(PDO::FETCH_ASSOC);

        $epaisseurs_par_material = [
            'mdf' => [4, 6, 8],
            'plexy' => [4, 6],
            'carton' => [3, 6, 8],
            'mousse' => [3, 6]
        ];

        echo '<div class="mb-3">';
        echo '<label for="material" class="form-label">Matériau 🪵 :</label>';
        echo '<select id="material" name="material" class="form-select" required>';
        echo '<option value="" disabled selected>Choisir un matériau</option>';
        foreach ($materials as $mat) {
            $nom_mat = strtolower($mat['name']);
            $eps = $epaisseurs_par_material[$nom_mat] ?? [];
            if ($eps) {
                foreach ($eps as $ep) {
                    echo '<option value="' . $mat['id'] . '_' . $ep . '">' . htmlspecialchars($mat['name']) . " {$ep}mm ({$mat['unit']}) - Stock: {$mat['stock']}" . '</option>';
                }
            } else {
                echo '<option value="' . $mat['id'] . '">' . htmlspecialchars($mat['name']) . " ({$mat['unit']}) - Stock: {$mat['stock']}" . '</option>';
            }
        }
        echo '</select></div>';
    }

    // Ajout du champ quantité utilisée (number, min=1, required)
    echo '<div class="mb-3">';
    echo '<label for="quantite" class="form-label">Quantité utilisée 👌 :</label>';
    $valQuantite = isset($_POST['quantite']) ? htmlspecialchars($_POST['quantite']) : '';
    echo '<input type="number" id="quantite" name="quantite" class="form-control" min="1" step="1" required value="' . $valQuantite . '">';
    echo '</div>';

    // Professeur
    echo '<div class="mb-3">';
    echo '<label for="professor_name" class="form-label">Professeur référent 🧙🏻‍♂️ :</label>';
    $stmtProf = $pdo->query("SELECT id, first_name, last_name FROM professors ORDER BY last_name, first_name");
    $profs = $stmtProf->fetchAll(PDO::FETCH_ASSOC);
    echo '<input list="professors_list" id="professor_name" name="professor_name" placeholder="Rechercher un professeur" class="form-control" required autocomplete="off">';
    echo '<datalist id="professors_list">';
    foreach ($profs as $p) {
        $fullname = $p['first_name'] . ' ' . $p['last_name'];
        echo '<option value="' . htmlspecialchars($fullname) . '">';
    }
    echo '</datalist></div>';

    // Responsable (dropdown)
    $stmtResp = $pdo->query("SELECT id, name FROM responsibles ORDER BY name");
    $responsables = $stmtResp->fetchAll(PDO::FETCH_ASSOC);
    echo '<div class="mb-3">';
    echo '<label for="responsible" class="form-label">Responsable 🕵🏻‍♂️ :</label>';
    echo '<select id="responsible" name="responsible" class="form-select" required>';
    echo '<option value="" disabled selected>Choisir un responsable</option>';
    foreach ($responsables as $r) {
        echo '<option value="' . $r['id'] . '">' . htmlspecialchars($r['name']) . '</option>';
    }
    echo '</select></div>';
    //liste classes 
    $stmtClasses = $pdo->query("SELECT id, name FROM classes ORDER BY name ASC");
    $classes = $stmtClasses->fetchAll(PDO::FETCH_ASSOC);

    echo '<label for="classe" class="form-label fw-semibold">Sélectionner une classe 📚 :</label>';
    echo '<select id="classe" name="classe" class="form-select">';
    echo '<option value="" disabled selected>Choisir une classe...</option>';
    foreach ($classes as $classe) {
        echo '<option value="' . $classe['id'] . '">' . htmlspecialchars($classe['name']) . '</option>';
    }
    echo '</select>';

    // Bouton d'envoi
    echo '<button type="submit" class="btn btn-primary" name="add_record">Ajouter</button>';
    echo '</form>';
}

// --- Ajout d'un enregistrement ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_record'])) {
    // Nettoyer les données POST
    $machine_id = (int)($_POST['machines'] ?? 0);
    $modele_id = (int)($_POST['modele'] ?? 0);
    $material_raw = $_POST['material'] ?? '';
    $quantite = (int)($_POST['quantite'] ?? 0);
    $professor_name = trim($_POST['professor_name'] ?? '');
    $responsible_id = (int)($_POST['responsible'] ?? 0);
    $classe_id = (int)($_POST['classe'] ?? 0);

    if ($machine_id > 0 && $quantite > 0 && $professor_name !== '' && $responsible_id > 0) {
        $material_id = null;
        $epaisseur = null;
        if (strpos($material_raw, '_') !== false) {
            list($material_id, $epaisseur) = explode('_', $material_raw);
            $material_id = (int)$material_id;
            $epaisseur = (int)$epaisseur;
        } else {
            $material_id = (int)$material_raw;
        }

        // Recherche de l'id du professeur (par nom complet)
        $stmtProfSearch = $pdo->prepare("SELECT id FROM professors WHERE CONCAT(first_name, ' ', last_name) = ?");
        $stmtProfSearch->execute([$professor_name]);
        $professor_id = $stmtProfSearch->fetchColumn();

        if (!$professor_id) {
            echo "<div class='alert alert-danger'>Professeur introuvable.</div>";
        } else {
            // Vérifier le stock du matériau (on autorise le stock négatif)
            $stmtStock = $pdo->prepare("SELECT stock FROM materials WHERE id = ?");
            $stmtStock->execute([$material_id]);
            $stock = $stmtStock->fetchColumn();

            if ($stock === false) {
                echo "<div class='alert alert-danger'>Matériau introuvable.</div>";
            } else {
                // Insertion dans la table enregistrements AVEC la colonne quantite
                $sqlInsert = "INSERT INTO enregistrements 
                    (machine_id, modele_id, material_id, epaisseur, professor_id, responsible_id, quantite,class_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtInsert = $pdo->prepare($sqlInsert);
                $stmtInsert->execute([
                    $machine_id,
                    $modele_id ?: null,
                    $material_id ?: null,
                    $epaisseur ?: null,
                    $professor_id,
                    $responsible_id,
                    $quantite,
                    $classe_id
                ]);

                // Mise à jour du stock (peut devenir négatif)
                $stmtUpdateStock = $pdo->prepare("UPDATE materials SET stock = stock - ? WHERE id = ?");
                $stmtUpdateStock->execute([$quantite, $material_id]);

                echo "<div class='alert alert-success'>Enregistrement ajouté et stock mis à jour.</div>";
                // Redirection pour éviter la resoumission
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Merci de remplir tous les champs obligatoires correctement.</div>";
    }
}

// === Tableau de toutes les entrées (toutes machines) ===
$sqlAll = "SELECT e.id, m.NAME AS machine_name, mo.name AS modele_name, 
        mat.name AS material_name, e.quantite, 
        CONCAT(p.first_name, ' ', p.last_name) AS professor_name, r.name AS responsible_name,
        c.name AS classe_name
        FROM enregistrements e
        LEFT JOIN machines m ON e.machine_id = m.ID
        LEFT JOIN modele mo ON e.modele_id = mo.id
        LEFT JOIN materials mat ON e.material_id = mat.id
        LEFT JOIN professors p ON e.professor_id = p.id
        LEFT JOIN responsibles r ON e.responsible_id = r.id
        LEFT JOIN classes c ON e.class_id = c.id
        ORDER BY e.id DESC";
$stmtAll = $pdo->query($sqlAll);
$allEntries = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

echo '<div class="mb-5">';
echo '<h5 class="titre-entrees fw-semibold">Toutes les entrées</h5>';
if ($allEntries) {
    echo '<form method="post" onsubmit="return confirm(\'Confirmer la suppression des entrées sélectionnées ?\');">';
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover table-bordered align-middle stylish-table">';
    echo '<thead class="table-primary">';
    echo '<tr>';
    echo '<th>Sélection</th><th>Machine</th><th>Modèle</th><th>Matériau / Variante</th><th>Quantité</th><th>Professeur</th><th>Responsable</th><th>Classe</th>';
    echo '</tr></thead><tbody>';
    foreach ($allEntries as $e) {
        echo '<tr>';
        echo '<td class="text-center"><input type="checkbox" name="delete_ids[]" value="' . $e['id'] . '"></td>';
        echo '<td>' . htmlspecialchars($e['machine_name']) . '</td>';
        echo '<td>' . htmlspecialchars($e['modele_name'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($e['material_name'] ?? '') . '</td>';
        echo '<td class="text-center">' . (int)$e['quantite'] . '</td>';
        echo '<td>' . htmlspecialchars($e['professor_name']) . '</td>';
        echo '<td>' . htmlspecialchars($e['responsible_name']) . '</td>';
        echo '<td>' . htmlspecialchars($e['classe_name'] ?? '') . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
    echo '<button type="submit" name="delete_selected" class="btn btn-danger mt-2">Supprimer sélection</button>';
    echo '</form>';
} else {
     echo "<p class='text-muted'>Aucune entrée enregistrée.</p>";
}
echo '</div>';
?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>