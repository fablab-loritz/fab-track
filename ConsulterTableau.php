<div align="center">
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Consultation Fab-Track</title>
    <link rel="stylesheet" href="TropBeau.css"/>
</head>
<body>
<div align="center">

<?php
require_once 'config.php';
$pdo = getPDO();

// Récupérer les totaux
$responsablesStmt = $pdo->query("
    SELECT r.name, COUNT(e.id) AS total
    FROM responsibles r
    LEFT JOIN enregistrements e ON e.responsible_id = r.id
    GROUP BY r.id
    ORDER BY r.name ASC
");
$responsables = $responsablesStmt->fetchAll(PDO::FETCH_ASSOC);

// Tableau des totaux
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

echo '<h4>Fab-Track - Consultation</h4>';

// Filtre machine
$stmt = $pdo->query("SELECT ID, NAME FROM machines");
$machinesList = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo '<form method="post">';
echo '<select name="machines" onchange="this.form.submit()">';
echo '<option value="" disabled selected>Sélectionner une machine</option>';
foreach ($machinesList as $machine) {
    $selected = (isset($_POST['machines']) && $_POST['machines'] == $machine['ID']) ? 'selected' : '';
    echo '<option value="' . $machine['ID'] . '" ' . $selected . '>' . htmlentities($machine['NAME']) . '</option>';
}
echo '</select>';
echo '</form>';

// Requête des enregistrements
if (isset($_POST['machines']) && is_numeric($_POST['machines'])) {
    $machine_id = intval($_POST['machines']);
    $stmt = $pdo->prepare("
        SELECT 
            m.name AS machine,
            mo.name AS modele,
            CONCAT(ma.name, IFNULL(CONCAT(' - ', v.description), '')) AS materiau,
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
        ORDER BY e.date_enregistrement DESC
    ");
    $stmt->execute([$machine_id]);
} else {
    $stmt = $pdo->query("
        SELECT 
            m.name AS machine,
            mo.name AS modele,
            CONCAT(ma.name, IFNULL(CONCAT(' - ', v.description), '')) AS materiau,
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
        ORDER BY e.date_enregistrement DESC
    ");
}
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Affichage du tableau
echo '<div style="max-height:400px; overflow:auto; margin-top:50px;">';
echo '<table border="1" cellspacing="0" cellpadding="3">';
echo '<tr>
    <th>Machine</th><th>Modèle</th><th>Matériau</th><th>Quantité</th><th>Date</th><th>Professeur</th><th>Classe</th><th>Responsable</th>
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
    echo '</tr>';
}
echo '</table>';
echo '</div>';

ob_end_flush();
?>
</div>
</body>
</html>
