<?php
session_start();
require_once 'config.php';
$pdo = getPDO();

// Images de carottes
$carottes = [
    "carotte1.png",
    "carotte2.png",
    "carotte3.png",
    "carotte4.png"
];

// Récupère la liste des responsables (depuis responsibles)
$responsables = $pdo->query("SELECT name FROM responsibles WHERE name IN ('Jeremy nouilles','Steven','Axel') ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);

// Gestion du responsable sélectionné
if (isset($_POST['responsable_id'])) {
    $_SESSION['carotte_responsable'] = $_POST['responsable_id'];
}
$selected_responsable = $_SESSION['carotte_responsable'] ?? '';

// Gestion du clic sur la carotte
if (isset($_POST['carotte_click']) && !empty($selected_responsable)) {
    if (!isset($_SESSION['carotte_index'])) $_SESSION['carotte_index'] = 0;
    $_SESSION['carotte_index'] = ($_SESSION['carotte_index'] + 1) % count($carottes);

    // Incrémente le compteur dans carotte_clicks
    $stmt = $pdo->prepare("INSERT INTO carotte_clicks (responsable, clicks) VALUES (?, 1)
        ON DUPLICATE KEY UPDATE clicks = clicks + 1");
    $stmt->execute([$selected_responsable]);
}
if (!isset($_SESSION['carotte_index'])) $_SESSION['carotte_index'] = 0;

// Récupère le leaderboard carotte
$leaderboard = $pdo->query("SELECT responsable, clicks FROM carotte_clicks WHERE responsable IN ('Jeremy nouilles','Steven','Axel') ORDER BY clicks DESC")->fetchAll(PDO::FETCH_ASSOC);

// Mode sombre
$darkmode = (!empty($_COOKIE['darkmode']) && $_COOKIE['darkmode'] === 'on');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Carotte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/custom.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="icones/capybara Parfait.png">
    <style>
        body {
            background: url('icones/carottewalpaper.png') center center / cover no-repeat fixed;
            min-height: 100vh;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: 0;
            background: inherit;
            filter: blur(8px) brightness(0.8);
            pointer-events: none;
        }
        .main-content-overlay {
            position: relative;
            z-index: 1;
            background: rgba(255,255,255,0.85);
            border-radius: 18px;
            padding: 2rem 1.5rem;
            margin-top: 2rem;
            box-shadow: 0 2px 16px rgba(44,161,219,0.08);
        }
        body.dark-mode .main-content-overlay {
            background: rgba(30,30,30,0.85);
        }
    </style>
</head>
<body<?php if ($darkmode) echo ' class="dark-mode"'; ?>>
<nav class="navbar navbar-light bg-white shadow-sm mb-4">
  <div class="container-fluid align-items-center d-flex">
    <a href="admin.php" class="btn btn-outline-primary me-2">Retour Admin</a>
    <span class="navbar-brand ms-auto fw-bold d-flex align-items-center">Carotte</span>
  </div>
</nav>
<div class="container py-4 d-flex">
    <div class="flex-grow-1 text-center main-content-overlay" style="max-width:700px; min-width:400px;">
        <form method="post" autocomplete="off">
            <div class="mb-3" style="max-width:350px;margin:auto;">
                <label for="responsable_id" class="form-label">Responsable :</label>
                <select name="responsable_id" id="responsable_id" class="form-select" required>
                    <option value="">Sélectionner un responsable...</option>
                    <?php foreach ($responsables as $resp): ?>
                        <option value="<?= htmlentities($resp) ?>" <?= ($resp == $selected_responsable ? 'selected' : '') ?>>
                            <?= htmlentities($resp) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="carotte_click" class="btn btn-dark btn-lg mb-4" style="padding: 1.5rem 2.5rem;" <?= empty($selected_responsable) ? 'disabled' : '' ?>>
                <img src="icones/<?= $carottes[$_SESSION['carotte_index']] ?>" alt="Carotte" style="height:320px;max-width:100%;">
                <div style="font-size:1.5rem;">Changer de carotte !</div>
            </button>
        </form>
    </div>
    <div class="main-content-overlay" style="min-width:250px;">
        <h4 class="mb-3">🥕 Leaderboard</h4>
        <ul class="list-group">
            <?php foreach ($leaderboard as $row): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($row['responsable']) ?>
                    <span class="badge bg-warning text-dark"><?= $row['clicks'] ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
</body>
</html>