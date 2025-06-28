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

// --- Gestion du combo ---
if (!isset($_SESSION['combo_responsable'])) $_SESSION['combo_responsable'] = $selected_responsable;
if (!isset($_SESSION['combo_count'])) $_SESSION['combo_count'] = 0;

// Si on change de responsable, on reset le combo
if ($_SESSION['combo_responsable'] !== $selected_responsable) {
    $_SESSION['combo_responsable'] = $selected_responsable;
    $_SESSION['combo_count'] = 0;
}

// Gestion du mot de passe pour le bouton palier
$show_palier_btn = false;
if (isset($_POST['show_palier_btn']) && $_POST['show_palier_btn'] === 'ezlevel') {
    $_SESSION['show_palier_btn'] = true;
}
if (!empty($_SESSION['show_palier_btn'])) {
    $show_palier_btn = true;
}

// Gestion du bouton "Aller au palier supérieur"
if (isset($_POST['goto_next_palier']) && !empty($selected_responsable) && $show_palier_btn) {
    $paliers = [100, 300, 500, 1000, 2000, 5000, 10000, 100000, 1000000];
    $current = $_SESSION['combo_count'];
    $next = null;
    foreach ($paliers as $palier) {
        if ($current < $palier) {
            $next = $palier;
            break;
        }
    }
    if ($next !== null) {
        $to_add = $next - $current;
        for ($i = 0; $i < $to_add; $i++) {
            $stmt = $pdo->prepare("INSERT INTO carotte_clicks (responsable, clicks) VALUES (?, 1)
                ON DUPLICATE KEY UPDATE clicks = clicks + 1");
            $stmt->execute([$selected_responsable]);
            $_SESSION['combo_count']++;
        }
        $palier_sons = [
            100 => 'palier100',
            300 => 'palier300',
            500 => 'palier500',
            1000 => 'palier1000',
            2000 => 'palier2000',
            5000 => 'palier5000',
            10000 => 'palier10k',
            100000 => 'palier100k',
            1000000 => 'palier1M'
        ];
        $_SESSION['palier_js_flash'] = $palier_sons[$next] ?? '';
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;
    }
}

// Gestion du clic sur la carotte
if (isset($_POST['carotte_click']) && !empty($selected_responsable)) {
    if (!isset($_SESSION['carotte_index'])) $_SESSION['carotte_index'] = 0;
    $_SESSION['carotte_index'] = ($_SESSION['carotte_index'] + 1) % count($carottes);

    // Incrémente le compteur dans carotte_clicks
    $stmt = $pdo->prepare("INSERT INTO carotte_clicks (responsable, clicks) VALUES (?, 1)
        ON DUPLICATE KEY UPDATE clicks = clicks + 1");
    $stmt->execute([$selected_responsable]);

    // Incrémente le combo
    $_SESSION['combo_count']++;
}
if (!isset($_SESSION['carotte_index'])) $_SESSION['carotte_index'] = 0;

$combo_count = $_SESSION['combo_count'];
$combo_message = '';
if ($combo_count >= 1000000) {
    $combo_message = '<div class="combo-message fw-bold text-warning" style="font-size:2rem;">🔥Combo "meme pas le plus rapide, pas tres bon en speedrun..." 🥸 x1000000 ! 🔥</div>';
} elseif ($combo_count >= 100000) {
    $combo_message = '<div class="combo-message fw-bold text-warning" style="font-size:2rem;">🔥Combo "The End ?" ! peut etre hein  🥸 x100000 ! 🔥</div>';
} elseif ($combo_count >= 10000) {
    $combo_message = '<div class="combo-message fw-bold text-warning" style="font-size:2rem;">🔥Combo de la terrible perte de temp ! aieeeeee 🥸 x10000 ! 🔥</div>';
} elseif ($combo_count >= 5000) {
    $combo_message = '<div class="combo-message fw-bold text-warning" style="font-size:2rem;">🔥Combo "licenciement sans indemnisations" ! dommage 🥸 x5000 ! 🔥</div>';
} elseif ($combo_count >= 2000) {
    $combo_message = '<div class="combo-message fw-bold text-warning" style="font-size:2rem;">🔥Combo "renvoi a effet immédiat !" Felicitations 🥸 x2000 ! 🔥</div>';
} elseif ($combo_count >= 1000) {
    $combo_message = '<div class="combo-message fw-bold text-warning" style="font-size:2rem;">🔥Combo "pire employé du Mois" x1000 ! 🔥</div>';
} elseif ($combo_count >= 500) {
    $combo_message = '<div class="combo-message fw-bold text-warning" style="font-size:2rem;">🔥Super Combo Chomage x500 ! 🔥</div>';
} elseif ($combo_count >= 300) {
    $combo_message = '<div class="combo-message fw-bold text-warning" style="font-size:2rem;">🔥Bon Combo Chomage x300 ! 🔥</div>';
} elseif ($combo_count >= 100) {
    $combo_message = '<div class="combo-message fw-bold text-warning" style="font-size:2rem;">🔥Combo Chomage x100 ! 🔥</div>';
}

// Succès
$achievements = [];
if ($combo_count >= 1)      $achievements[] = "Premier clic !";
if ($combo_count >= 100)    $achievements[] = "Combo 100 !";
if ($combo_count >= 300)    $achievements[] = "Combo 300 !";
if ($combo_count >= 500)    $achievements[] = "Combo 500 !";
if ($combo_count >= 1000)   $achievements[] = "Combo 1000 !";
if ($combo_count >= 2000)   $achievements[] = "Combo 2000 !";
if ($combo_count >= 5000)   $achievements[] = "Combo 5000 !";
if ($combo_count >= 10000)  $achievements[] = "Combo 10 000 !";
if ($combo_count >= 100000) $achievements[] = "Combo 100 000 !";
if ($combo_count >= 1000000)$achievements[] = "Combo 1 000 000 !";

// Meilleur combo (session)
if (!isset($_SESSION['best_combo'])) $_SESSION['best_combo'] = 0;
if ($combo_count > $_SESSION['best_combo']) $_SESSION['best_combo'] = $combo_count;
$best_combo = $_SESSION['best_combo'];

// Barre de progression vers prochain palier
$paliers = [100, 300, 500, 1000, 2000, 5000, 10000, 100000, 1000000];
$palier_suivant = 0;
foreach ($paliers as $palier) {
    if ($combo_count < $palier) {
        $palier_suivant = $palier;
        break;
    }
}
$progress = $palier_suivant ? min(100, round(($combo_count / $palier_suivant) * 100)) : 100;

// Récupère le leaderboard carotte
$leaderboard = $pdo->query("SELECT responsable, clicks FROM carotte_clicks WHERE responsable IN ('Jeremy nouilles','Steven','Axel') ORDER BY clicks DESC")->fetchAll(PDO::FETCH_ASSOC);

// Total global
$total_clicks = $pdo->query("SELECT SUM(clicks) FROM carotte_clicks")->fetchColumn();

// Mode sombre
$darkmode = (!empty($_COOKIE['darkmode']) && $_COOKIE['darkmode'] === 'on');

// Pour JS : palier atteint
$palier_sons = [
    100 => 'palier100',
    300 => 'palier300',
    500 => 'palier500',
    1000 => 'palier1000',
    2000 => 'palier2000',
    5000 => 'palier5000',
    10000 => 'palier10k',
    100000 => 'palier100k',
    1000000 => 'palier1M'
];
if (isset($_SESSION['palier_js_flash'])) {
    $palier_js = $_SESSION['palier_js_flash'];
    unset($_SESSION['palier_js_flash']);
} else {
    $palier_js = (array_key_exists($combo_count, $palier_sons)) ? $palier_sons[$combo_count] : '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Carotte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/custom.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="icones/logo-fab-track.ico">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <style>
        html, body { height: 100%; }
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
        .carotte-btn {
            transition: transform 0.08s, box-shadow 0.08s;
            box-shadow: 0 4px 24px 0 rgba(255,140,0,0.25);
            border-radius: 2em;
            background: linear-gradient(90deg, #ffb347 0%, #ffcc33 100%);
            color: #222;
            font-weight: bold;
            font-size: 2rem;
            padding: 1.5rem 2.5rem;
            cursor: pointer;
            border: none;
            outline: none;
            position: relative;
            font-family: 'Fredoka One', cursive;
        }
        .carotte-btn:active {
            transform: scale(0.93);
            box-shadow: 0 2px 8px 0 rgba(255,140,0,0.18);
        }
        .carotte-btn:hover {
            filter: brightness(1.08);
        }
        .carotte-img {
            height: 320px;
            max-width: 100%;
            transition: transform 0.1s;
        }
        .carotte-btn:active .carotte-img {
            transform: scale(1.08) rotate(-8deg);
        }
        #carotte-counter {
            font-size: 3rem;
            color: #ff9800;
            text-shadow: 0 2px 8px #fff7;
            font-family: 'Fredoka One', cursive;
        }
        .combo-message {
            animation: pop 0.5s;
        }
        @keyframes pop {
            0% { transform: scale(0.7);}
            60% { transform: scale(1.2);}
            100% { transform: scale(1);}
        }
    </style>
</head>
<body<?php if ($darkmode) echo ' class="dark-mode"'; ?>>
<nav class="navbar navbar-light bg-white shadow-sm mb-4">
  <div class="container-fluid align-items-center d-flex">
    <a href="admin.php" class="btn btn-outline-primary me-2">Retour Admin</a>
    <span class="navbar-brand ms-auto fw-bold d-flex align-items-center">Carotte 🏴‍☠️</span>
  </div>
</nav>
<div class="d-flex w-100" style="min-height: 80vh; align-items: stretch;">
    <!-- Combo à gauche -->
    <div class="main-content-overlay d-flex flex-column align-items-center justify-content-center"
         style="min-width:220px;max-width:260px;flex-shrink:0;">
        <div class="display-6 mb-2" style="color:#ff9800;">Chomage Bonus</div>
        <div class="fw-bold" style="font-size:2.5rem; color:#ff9800; text-shadow:0 2px 8px #fff7;"><?= $combo_count ?></div>
        <?= $combo_message ?>
    </div>
    <!-- Jeu au centre -->
    <div class="main-content-overlay flex-grow-1 text-center d-flex flex-column justify-content-center"
         style="max-width:1000px; min-width:500px; margin-left:auto; margin-right:auto;">
        <form method="post" autocomplete="off">
            <div class="mb-3" style="max-width:350px;margin:auto;">
                <label for="responsable_id" class="form-label">Responsable :</label>
                <select name="responsable_id" id="responsable_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">Sélectionner un responsable...</option>
                    <?php foreach ($responsables as $resp): ?>
                        <option value="<?= htmlentities($resp) ?>" <?= ($resp == $selected_responsable ? 'selected' : '') ?>>
                            <?= htmlentities($resp) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($palier_suivant): ?>
                <div class="progress my-3" style="height: 22px; width: 100%; max-width: 400px; margin-left:auto; margin-right:auto;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $progress ?>%;" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                        <?= $progress ?>%
                    </div>
                </div>
                <div style="font-size:1rem; margin-bottom:1rem;">Prochain palier : <?= $palier_suivant ?></div>
            <?php endif; ?>
            <button type="submit" name="carotte_click" class="carotte-btn" <?= empty($selected_responsable) ? 'disabled' : '' ?>>
                <div id="carotte-counter"><?= $leaderboard[0]['clicks'] ?? 0 ?></div>
                <img src="icones/<?= $carottes[$_SESSION['carotte_index']] ?>" class="carotte-img" alt="Carotte">
                <div style="font-size:1.5rem;">Une Carotte ?</div>
            </button>
            <!-- Zone mot de passe ou bouton palier -->
            <div class="d-flex justify-content-center align-items-center gap-2 mt-3">
                <?php if (!$show_palier_btn): ?>
                    <input type="password" name="show_palier_btn" class="form-control" style="max-width:140px;" placeholder="Mot de passe admin">
                    <button type="submit" class="btn btn-secondary btn-sm" style="margin-top:-1rem;">Valider</button>
                <?php else: ?>
                    <button type="submit" name="goto_next_palier" class="btn btn-danger btn-sm" style="margin-top:-1rem;">Aller au palier supérieur</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <!-- Leaderboard à droite -->
    <div class="main-content-overlay d-flex flex-column align-items-center justify-content-start"
         style="min-width:250px;max-width:320px;flex-shrink:0;">
        <h4 class="mb-3">🥕 Leaderboard</h4>
        <div class="mb-2 text-primary fw-bold">Total global : <?= $total_clicks ?> 🥕</div>
        <ul class="list-group mb-3 w-100">
            <?php foreach ($leaderboard as $row): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($row['responsable']) ?>
                    <span class="badge bg-warning text-dark"><?= $row['clicks'] ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="mb-2 text-secondary" style="font-size:1.1rem;">Meilleur combo (session) : <span class="fw-bold"><?= $best_combo ?></span></div>
        <?php if ($achievements): ?>
            <div class="mt-2">
                <div class="fw-bold mb-1">Succès :</div>
                <?php foreach ($achievements as $ach): ?>
                    <div class="badge bg-success mb-1"><?= $ach ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- SONS DE PALIERS -->
<audio id="carotte-sound" src="icones/clic.mp3" preload="auto"></audio>
<audio id="palier100" src="icones/palier100.mp3"></audio>
<audio id="palier300" src="icones/palier300.mp3"></audio>
<audio id="palier500" src="icones/palier500.mp3"></audio>
<audio id="palier1000" src="icones/palier1000.mp3"></audio>
<audio id="palier2000" src="icones/palier2000.mp3"></audio>
<audio id="palier5000" src="icones/palier5000.mp3"></audio>
<audio id="palier10k" src="icones/palier10k.mp3"></audio>
<audio id="palier100k" src="icones/palier100k.mp3"></audio>
<audio id="palier1M" src="icones/palier1M.mp3"></audio>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Son au clic
    const btn = document.querySelector('.carotte-btn');
    if(btn) {
        btn.addEventListener('click', function() {
            const audio = document.getElementById('carotte-sound');
            if(audio) {
                audio.currentTime = 0;
                audio.play();
            }
        });
    }
    // Confettis et sons sur palier combo
    var palierSon = <?= json_encode($palier_js) ?>;
    if (palierSon) {
        confetti({particleCount: 10000, spread: 10000, origin: { y: 0.5 }});
        var palierAudio = document.getElementById(palierSon);
        if(palierAudio) {
            palierAudio.currentTime = 0;
            palierAudio.play();
        }
    }
});
</script>
</body>
</html>