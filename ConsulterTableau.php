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

// Récupération des machines
$stmt = $pdo->query("SELECT ID, NAME FROM machines");
$machinesList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ----------- Début ajout graphique barres -----------

// Récupération des classes
$classesList = $pdo->query("SELECT id, name FROM classes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Récupération des matériaux
$materialsList = $pdo->query("SELECT id, name FROM materials ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Initialisation des sélections pour chaque graphique
if (isset($_POST['show_graph'])) {
    $selectedClass = $_POST['graph_class'];
    $selectedMaterial = $_POST['graph_material'];
} else {
    $selectedClass = 'all';
    $selectedMaterial = 'all';
}

if (isset($_POST['show_pie'])) {
    $selectedClassPie = $_POST['pie_class'];
    $selectedMaterialPie = $_POST['pie_material'];
} else {
    $selectedClassPie = 'all';
    $selectedMaterialPie = 'all';
}

// Préparation des données pour le graphique à barres
$year = date('Y');
$params = [$year];
$where = "";

if ($selectedClass !== 'all') {
    $where .= " AND e.class_id = ? ";
    $params[] = $selectedClass;
}
if ($selectedMaterial !== 'all') {
    $where .= " AND e.material_id = ? ";
    $params[] = $selectedMaterial;
}

$stmtGraph = $pdo->prepare("
    SELECT 
        MONTH(e.date_enregistrement) AS mois,
        SUM(e.quantite) AS total
    FROM enregistrements e
    WHERE YEAR(e.date_enregistrement) = ? $where
    GROUP BY mois
    ORDER BY mois
");
$stmtGraph->execute($params);
$graphData = $stmtGraph->fetchAll(PDO::FETCH_ASSOC);

// Préparer les labels et valeurs pour Chart.js
$labels = [];
$values = [];
for ($i = 1; $i <= 12; $i++) {
    $labels[] = date('M', mktime(0,0,0,$i,1));
    $values[] = 0;
}
foreach ($graphData as $row) {
    $values[$row['mois']-1] = (int)$row['total'];
}
if (empty($labels)) $labels = [];
if (empty($values)) $values = [];
// ----------- Fin ajout graphique barres -----------

// ----------- Début ajout camembert -----------

// Préparation des données pour le camembert
$paramsPie = [];
$wherePie = "";

if ($selectedClassPie !== 'all') {
    $wherePie .= " AND e.class_id = ? ";
    $paramsPie[] = $selectedClassPie;
}
if ($selectedMaterialPie !== 'all') {
    $wherePie .= " AND e.material_id = ? ";
    $paramsPie[] = $selectedMaterialPie;
}

// Si "Tous les matériaux" => répartition par matériau, sinon par classe
if ($selectedMaterialPie === 'all') {
    $sqlPie = "
        SELECT ma.name AS label, SUM(e.quantite) AS total
        FROM enregistrements e
        JOIN materials ma ON e.material_id = ma.id
        WHERE 1=1 $wherePie
        GROUP BY ma.name
        ORDER BY ma.name
    ";
} else {
    $sqlPie = "
        SELECT c.name AS label, SUM(e.quantite) AS total
        FROM enregistrements e
        JOIN classes c ON e.class_id = c.id
        WHERE 1=1 $wherePie
        GROUP BY c.name
        ORDER BY c.name
    ";
}
$stmtPie = $pdo->prepare($sqlPie);
$stmtPie->execute($paramsPie);
$pieData = $stmtPie->fetchAll(PDO::FETCH_ASSOC);

$pieLabels = [];
$pieValues = [];
foreach ($pieData as $row) {
    $pieLabels[] = $row['label'];
    $pieValues[] = (int)$row['total'];
}
if (empty($pieLabels)) $pieLabels = [];
if (empty($pieValues)) $pieValues = [];
// ----------- Fin ajout camembert -----------

// Récupération des enregistrements selon la machine sélectionnée
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Consultation Fab-Track</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/custom.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<link rel="icon" type="image/x-icon" href="icones/logo-fab-track.ico">
<body<?php
    $classes = [];
    if ($darkmode) $classes[] = 'dark-mode';
    if ($classes) echo ' class="' . implode(' ', $classes) . '"';
?>>
<nav class="navbar navbar-light bg-white shadow-sm mb-4">
  <div class="container-fluid align-items-center d-flex">
    <?php
      $toggleUrl = $_SERVER['PHP_SELF'] . '?darkmode=' . ($darkmode ? 'off' : 'on');
    ?>
    <a href="<?= htmlspecialchars($toggleUrl) ?>" class="btn btn-outline-primary me-2">
        <?= $darkmode ? 'Mode clair' : 'Mode sombre' ?>
    </a>
    <a href="admin.php" class="btn btn-outline-secondary me-2">Admin</a>
    <a href="Fab-Track.php" class="btn btn-outline-secondary me-2">FabTrack</a>
    <a href="GestionStock.php" class="btn btn-outline-secondary me-2">Gestion Stock</a>
    <span class="navbar-brand ms-auto fw-bold d-flex align-items-center">Consultation</a>
        <div class="logo-fabtrack-float ms-4">
            <img src="icones/logo-fab-track.ico" alt="Logo Fab-Track" class="logo-light" style="height: 100px; width: auto;">
            <img src="icones/logo-fab-track-Sombre.ico" alt="Logo Fab-Track sombre" class="logo-dark" style="height: 100px; width: auto;">
        </div>
    </span>
  </div>
</nav>

    <!-- Le tableau en premier -->
    <form method="post" class="mb-4">
        <select name="machines" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
            <option value="" disabled selected>Sélectionner une machine</option>
            <?php foreach ($machinesList as $machine): ?>
                <option value="<?= $machine['ID'] ?>" <?= (isset($_POST['machines']) && $_POST['machines'] == $machine['ID']) ? 'selected' : '' ?>>
                    <?= htmlentities($machine['NAME']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
    <div class="table-responsive" style="max-height:400px; overflow:auto;">
        <table class="table table-bordered table-sm align-middle rounded-table">
            <thead>
                <tr>
                    <th>Machine</th>
                    <th>Modèle</th>
                    <th>Matériau</th>
                    <th>Quantité</th>
                    <th>Date</th>
                    <th>Professeur</th>
                    <th>Classe</th>
                    <th>Responsable</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                <tr>
                    <td><?= htmlentities($entry['machine']) ?></td>
                    <td><?= htmlentities($entry['modele'] ?? '') ?></td>
                    <td><?= htmlentities($entry['materiau']) ?></td>
                    <td><?= (int)$entry['quantite'] ?></td>
                    <td><?= htmlentities($entry['date_enregistrement']) ?></td>
                    <td><?= htmlentities($entry['professeur'] ?? '') ?></td>
                    <td><?= htmlentities($entry['classe'] ?? '') ?></td>
                    <td><?= htmlentities($entry['preparateur'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bloc graphique à barres EN DESSOUS du tableau -->
    <div class="mb-5 mt-5">
        <form method="post" class="row g-2 align-items-end mb-3">
            <div class="col-auto">
                <label for="graph_class" class="form-label mb-0">Classe :</label>
                <select name="graph_class" id="graph_class" class="form-select">
                    <option value="all" <?= $selectedClass === 'all' ? 'selected' : '' ?>>Toutes les classes</option>
                    <?php foreach ($classesList as $cl): ?>
                        <option value="<?= $cl['id'] ?>" <?= $selectedClass == $cl['id'] ? 'selected' : '' ?>>
                            <?= htmlentities($cl['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <label for="graph_material" class="form-label mb-0">Matériau :</label>
                <select name="graph_material" id="graph_material" class="form-select">
                    <option value="all" <?= $selectedMaterial === 'all' ? 'selected' : '' ?>>Tous les matériaux</option>
                    <?php foreach ($materialsList as $mat): ?>
                        <option value="<?= $mat['id'] ?>" <?= $selectedMaterial == $mat['id'] ? 'selected' : '' ?>>
                            <?= htmlentities($mat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" name="show_graph" class="btn btn-primary">Valider</button>
            </div>
        </form>
        <h5 class="text-center mb-3">Consommation par mois (<?= $year ?>)</h5>
        <canvas id="consoChart" height="80"></canvas>
    </div>
    <!-- Fin bloc graphique à barres -->

    <!-- Délimitation graphique camembert -->
    <hr class="my-5">
    <div class="mb-5">
        <form method="post" class="row g-2 align-items-end mb-3">
            <div class="col-auto">
                <label for="pie_class" class="form-label mb-0">Classe :</label>
                <select name="pie_class" id="pie_class" class="form-select">
                    <option value="all" <?= $selectedClassPie === 'all' ? 'selected' : '' ?>>Toutes les classes</option>
                    <?php foreach ($classesList as $cl): ?>
                        <option value="<?= $cl['id'] ?>" <?= $selectedClassPie == $cl['id'] ? 'selected' : '' ?>>
                            <?= htmlentities($cl['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <label for="pie_material" class="form-label mb-0">Matériau :</label>
                <select name="pie_material" id="pie_material" class="form-select">
                    <option value="all" <?= $selectedMaterialPie === 'all' ? 'selected' : '' ?>>Tous les matériaux</option>
                    <?php foreach ($materialsList as $mat): ?>
                        <option value="<?= $mat['id'] ?>" <?= $selectedMaterialPie == $mat['id'] ? 'selected' : '' ?>>
                            <?= htmlentities($mat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" name="show_pie" class="btn btn-primary">Valider</button>
            </div>
        </form>
        <h5 class="text-center mb-3">
            Répartition des consommations
            <?php if ($selectedMaterialPie === 'all'): ?>
                par matériau
            <?php else: ?>
                par classe
            <?php endif; ?>
        </h5>
        <div class="d-flex justify-content-center">
            <canvas id="pieChart" class="small-pie-chart"></canvas>
        </div>
    </div>
    <!-- Fin bloc camembert -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Détection du mode sombre
const isDark = document.body.classList.contains('dark-mode');
const barColor = isDark ? 'rgba(219, 164, 44, 0.7)' : 'rgba(54, 162, 235, 0.7)';
const barBorder = isDark ? 'rgba(219, 164, 44, 1)' : 'rgba(54, 162, 235, 1)';
const pieColors = isDark
    ? ['#DBA42C', '#DB6F2C', '#2CDBA4', '#2C6FDB', '#A42CDB', '#DB2C6F', '#6FDB2C', '#DB2CA4', '#2CA46F', '#A4DB2C', '#2CDB6F', '#6F2CDB']
    : ['#1976d2', '#2196f3', '#64b5f6', '#90caf9', '#1565c0', '#42a5f5', '#1e88e5', '#0d47a1', '#82b1ff', '#2962ff', '#448aff', '#80d8ff'];

const ctx = document.getElementById('consoChart').getContext('2d');
const consoChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Quantité consommée',
            data: <?= json_encode($values) ?>,
            backgroundColor: barColor,
            borderColor: barBorder,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

const pieCtx = document.getElementById('pieChart').getContext('2d');
const pieChart = new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($pieLabels) ?>,
        datasets: [{
            data: <?= json_encode($pieValues) ?>,
            backgroundColor: pieColors,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
</body>
<link rel="icon" type="image/x-icon" href="icones/logo-fab-track.ico">
</html>