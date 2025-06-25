<?php
require_once 'config.php';
$pdo = getPDO();

// Gestion du mode sombre via cookie
if (isset($_GET['darkmode'])) {
    setcookie('darkmode', $_GET['darkmode'], time() + 365*24*3600, "/");
    $_COOKIE['darkmode'] = $_GET['darkmode']; // Pour effet immédiat
}
$darkmode = (!empty($_COOKIE['darkmode']) && $_COOKIE['darkmode'] === 'on');

// Ajout professeur
if (isset($_POST['add_prof'])) {
    $stmt = $pdo->prepare("INSERT INTO professors (first_name, last_name) VALUES (?, ?)");
    $stmt->execute([$_POST['first_name'], $_POST['last_name']]);
    echo "<div class='alert alert-success'>Professeur ajouté !</div>";
}

// Suppression professeur
if (isset($_POST['delete_prof'])) {
    $stmt = $pdo->prepare("DELETE FROM professors WHERE id = ?");
    $stmt->execute([$_POST['prof_id']]);
    echo "<div class='alert alert-warning'>Professeur supprimé !</div>";
}

// Ajout classe
if (isset($_POST['add_class'])) {
    $stmt = $pdo->prepare("INSERT INTO classes (name) VALUES (?)");
    $stmt->execute([$_POST['class_name']]);
    echo "<div class='alert alert-success'>Classe ajoutée !</div>";
}

// Suppression classe
if (isset($_POST['delete_class'])) {
    $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
    $stmt->execute([$_POST['class_id']]);
    echo "<div class='alert alert-warning'>Classe supprimée !</div>";
}

// Ajout machine
if (isset($_POST['add_machine'])) {
    $stmt = $pdo->prepare("INSERT INTO machines (name, category) VALUES (?, ?)");
    $stmt->execute([$_POST['machine_name'], $_POST['machine_category']]);
    echo "<div class='alert alert-success'>Machine ajoutée !</div>";
}

// Suppression machine
if (isset($_POST['delete_machine'])) {
    $stmt = $pdo->prepare("DELETE FROM machines WHERE id = ?");
    $stmt->execute([$_POST['machine_id']]);
    echo "<div class='alert alert-warning'>Machine supprimée !</div>";
}

// Ajout matériau avec gestion du nom de fichier image
if (isset($_POST['add_material'])) {
    $imageName = trim($_POST['material_image'] ?? '');
    $stmt = $pdo->prepare("INSERT INTO materials (name, unit, stock, material_type_id, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['material_name'],
        $_POST['material_unit'],
        $_POST['material_stock'],
        $_POST['material_type_id'],
        $imageName
    ]);
    echo "<div class='alert alert-success'>Matériau ajouté !</div>";
}

// Suppression matériau avec suppression de l'image associée
if (isset($_POST['delete_material'])) {
    $stmt = $pdo->prepare("SELECT image FROM materials WHERE id = ?");
    $stmt->execute([$_POST['material_id']]);
    $img = $stmt->fetchColumn();
    $stmt = $pdo->prepare("DELETE FROM materials WHERE id = ?");
    $stmt->execute([$_POST['material_id']]);
    if ($img && $img !== 'default.png' && file_exists(__DIR__ . '/icones/' . $img)) {
        unlink(__DIR__ . '/icones/' . $img);
    }
    echo "<div class='alert alert-warning'>Matériau supprimé !</div>";
}

// Ajout variante (liée à un matériau)
if (isset($_POST['add_variante'])) {
    $stmt = $pdo->prepare("INSERT INTO variantes (id_materiaux, description, stock) VALUES (?, ?, 0)");
    $stmt->execute([
        $_POST['variante_material_id'],
        $_POST['variante_desc']
    ]);
    echo "<div class='alert alert-success'>Variante ajoutée !</div>";
}

// Suppression variante
if (isset($_POST['delete_variante'])) {
    $stmt = $pdo->prepare("DELETE FROM variantes WHERE id = ?");
    $stmt->execute([$_POST['variante_id']]);
    echo "<div class='alert alert-warning'>Variante supprimée !</div>";
}

// Récupérer dynamiquement les types de matériaux
$types = $pdo->query("SELECT id, name FROM material_types ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les listes pour les listes déroulantes
$profs = $pdo->query("SELECT id, first_name, last_name FROM professors ORDER BY last_name")->fetchAll(PDO::FETCH_ASSOC);
$classes = $pdo->query("SELECT id, name FROM classes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$machines = $pdo->query("SELECT id, name FROM machines ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$materials = $pdo->query("SELECT id, name, image FROM materials ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Pour chaque matériau, récupérer ses variantes
$variantes_by_material = [];
foreach ($materials as $mat) {
    $stmt = $pdo->prepare("SELECT id, description FROM variantes WHERE id_materiaux = ?");
    $stmt->execute([$mat['id']]);
    $variantes_by_material[$mat['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Administration - Ajout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/custom.css" rel="stylesheet" />
</head>
<link rel="icon" type="image/x-icon" href="icones/logo-fab-track.ico">
<body<?php
    $body_classes = [];
    if ($darkmode) $body_classes[] = 'dark-mode';
    if ($body_classes) echo ' class="' . implode(' ', $body_classes) . '"';
?>>
    <div class="logo-fabtrack-float">
        <img src="icones/logo-fab-track.ico" alt="Logo Fab-Track" class="logo-light">
        <img src="icones/logo-fab-track-Sombre.ico" alt="Logo Fab-Track sombre" class="logo-dark">
    </div>
    <nav class="navbar navbar-light bg-white shadow-sm mb-4">
      <div class="container-fluid">
        <?php
        $toggleUrl = $_SERVER['PHP_SELF'] . '?darkmode=' . ($darkmode ? 'off' : 'on');
        ?>
        <a href="<?= htmlspecialchars($toggleUrl) ?>" class="btn btn-outline-primary">
            <?= $darkmode ? 'Mode clair' : 'Mode sombre' ?>
            <a href="ConsulterTableau.php" class="btn btn-outline-secondary me-2">Consulter Tableau</a>
            <a href="Fab-Track.php" class="btn btn-outline-secondary me-2">FabTrack</a>
            <a href="GestionStock.php" class="btn btn-outline-secondary me-2">Gestion Stock</a>
        </a>
        <span class="navbar-brand">Administration</span>
      </div>
    </nav>
    <div id="main-content">
        <div class="container py-4">
            <h1 class="mb-4 titre-entrees text-center">Administration : Ajout d'éléments</h1>

            <!-- Professeurs -->
            <div class="border p-4 rounded shadow-sm bg-white mb-4 admin-row">
                <form method="post" class="admin-form" autocomplete="off">
                    <h2 class="mb-3">Ajouter un professeur</h2>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">Prénom</label>
                            <input type="text" name="first_name" id="first_name" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Nom</label>
                            <input type="text" name="last_name" id="last_name" required class="form-control">
                        </div>
                    </div>
                    <button type="submit" name="add_prof" class="btn btn-primary mt-3">Ajouter</button>
                </form>
                <form method="post" class="admin-delete d-flex align-items-center">
                    <select name="prof_id" class="form-select me-2" required>
                        <option value="">Supprimer un professeur...</option>
                        <?php foreach($profs as $prof): ?>
                            <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['first_name'].' '.$prof['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="delete_prof" class="btn btn-danger">Supprimer</button>
                </form>
            </div>

            <!-- Classes -->
            <div class="border p-4 rounded shadow-sm bg-white mb-4 admin-row">
                <form method="post" class="admin-form" autocomplete="off">
                    <h2 class="mb-3">Ajouter une classe</h2>
                    <label for="class_name" class="form-label">Nom de la classe</label>
                    <input type="text" name="class_name" id="class_name" required class="form-control mb-2">
                    <button type="submit" name="add_class" class="btn btn-primary">Ajouter</button>
                </form>
                <form method="post" class="admin-delete d-flex align-items-center">
                    <select name="class_id" class="form-select me-2" required>
                        <option value="">Supprimer une classe...</option>
                        <?php foreach($classes as $class): ?>
                            <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="delete_class" class="btn btn-danger">Supprimer</button>
                </form>
            </div>

            <!-- Machines -->
            <div class="border p-4 rounded shadow-sm bg-white mb-4 admin-row">
                <form method="post" class="admin-form" autocomplete="off">
                    <h2 class="mb-3">Ajouter une machine</h2>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="machine_name" class="form-label">Nom de la machine</label>
                            <input type="text" name="machine_name" id="machine_name" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="machine_category" class="form-label">Catégorie</label>
                            <input type="text" name="machine_category" id="machine_category" required class="form-control">
                        </div>
                    </div>
                    <button type="submit" name="add_machine" class="btn btn-primary mt-3">Ajouter</button>
                </form>
                <form method="post" class="admin-delete d-flex align-items-center">
                    <select name="machine_id" class="form-select me-2" required>
                        <option value="">Supprimer une machine...</option>
                        <?php foreach($machines as $machine): ?>
                            <option value="<?= $machine['id'] ?>"><?= htmlspecialchars($machine['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="delete_machine" class="btn btn-danger">Supprimer</button>
                </form>
            </div>

            <!-- Matériaux (avec variantes incluses) -->
            <div class="border p-4 rounded shadow-sm bg-white mb-4 admin-row">
                <form method="post" class="admin-form" autocomplete="off">
                    <h2 class="mb-3">Ajouter un matériau</h2>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label for="material_name" class="form-label">Nom du matériau</label>
                            <input type="text" name="material_name" id="material_name" required class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="material_unit" class="form-label">Unité</label>
                            <input type="text" name="material_unit" id="material_unit" required class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="material_stock" class="form-label">Stock initial</label>
                            <input type="number" name="material_stock" id="material_stock" required class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="material_type_id" class="form-label">Type matériau</label>
                            <select name="material_type_id" id="material_type_id" required class="form-select mb-2">
                                <option value="">Choisir un type...</option>
                                <?php foreach($types as $type): ?>
                                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <label for="material_image" class="form-label">Nom du fichier image (ex: petg.png)</label>
                            <input type="text" name="material_image" id="material_image" class="form-control" placeholder="petg.png">
                            <small class="text-muted">Dépose le fichier dans le dossier <b>icones/</b></small>
                        </div>
                    </div>
                    <button type="submit" name="add_material" class="btn btn-primary mt-3">Ajouter</button>
                </form>
                <form method="post" class="admin-delete d-flex align-items-center flex-column">
                    <div class="w-100 d-flex align-items-center mb-2">
                        <select name="material_id" class="form-select me-2" required>
                            <option value="">Supprimer un matériau...</option>
                            <?php foreach($materials as $mat): ?>
                                <option value="<?= $mat['id'] ?>"><?= htmlspecialchars($mat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="delete_material" class="btn btn-danger">Supprimer</button>
                    </div>
                </form>
            </div>

            <!-- Variantes par matériau -->
            <div class="border p-4 rounded shadow-sm bg-white mb-4">
                <h2 class="mb-3">Gérer les variantes de matériaux</h2>
                <div class="row">
                    <div class="col-md-6">
                        <form method="post" class="mb-3 d-flex align-items-end" autocomplete="off" style="gap:0.5em;">
                            <div>
                                <label for="variante_material_id" class="form-label">Matériau</label>
                                <select name="variante_material_id" id="variante_material_id" class="form-select" required>
                                    <option value="">Choisir un matériau...</option>
                                    <?php foreach($materials as $mat): ?>
                                        <option value="<?= $mat['id'] ?>"><?= htmlspecialchars($mat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="variante_desc" class="form-label">Description</label>
                                <input type="text" name="variante_desc" id="variante_desc" required class="form-control" placeholder="ex: 3 mm, A4...">
                            </div>
                            <button type="submit" name="add_variante" class="btn btn-outline-primary">Ajouter variante</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="post" class="d-flex align-items-end" style="gap:0.5em;">
                            <div>
                                <label for="variante_id" class="form-label">Supprimer une variante</label>
                                <select name="variante_id" id="variante_id" class="form-select" required>
                                    <option value="">Choisir une variante...</option>
                                    <?php foreach($materials as $mat): ?>
                                        <?php foreach($variantes_by_material[$mat['id']] as $var): ?>
                                            <option value="<?= $var['id'] ?>">
                                                <?= htmlspecialchars($mat['name']) ?> - <?= htmlspecialchars($var['description']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="delete_variante" class="btn btn-outline-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <?php foreach($materials as $mat): ?>
                        <div class="col-md-4 mb-3">
                            <strong><?= htmlspecialchars($mat['name']) ?></strong>
                            <ul class="material-type-list">
                                <?php foreach($variantes_by_material[$mat['id']] as $var): ?>
                                    <li><?= htmlspecialchars($var['description']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>