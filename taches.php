<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];


$tachemodif = null;
if (isset($_GET['modifier'])) {
    $stmt = $pdo->prepare("SELECT * FROM taches WHERE id=?");
    $stmt->execute([$_GET['modifier']]);
    $tachemodif = $stmt->fetch(PDO::FETCH_ASSOC);
}


if ($user['role'] == 'admin') {
    $taches = $pdo->query(
        "SELECT taches.*, users.username 
         FROM taches 
         JOIN users ON taches.user_id = users.id 
         ORDER BY taches.date_creation DESC"
    )->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT * FROM taches WHERE user_id=? ORDER BY date_creation DESC");
    $stmt->execute([$user['id']]);
    $taches = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des tâches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <h1 class="text-center mb-3">Gestion des tâches</h1>

    <p class="text-center">
        Connecté en tant que :
        <b><?= htmlspecialchars($user['username']) ?></b>
        (<?= $user['role'] ?>)
    </p>

    <div class="text-center mb-4">
        <a href="logout.php" class="btn btn-danger">Déconnexion</a>
    </div>

    <?php if ($user['role'] != 'admin'): ?>
        <div class="card mb-4 col-md-6 offset-md-3">
            <div class="card-header bg-primary text-white">
                <?= $tachemodif ? "Modifier une tâche" : "Ajouter une tâche" ?>
            </div>

            <div class="card-body">
                <form method="POST" action="actions.php">

                    <input type="hidden" name="action" value="<?= $tachemodif? "modifier" : "ajouter" ?>">

                    <?php if ($tachemodif): ?>
                        <input type="hidden" name="id" value="<?= $tachemodif['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Titre</label>
                        <input type="text" name="titre" class="form-control" required
                               value="<?= $tachemodif['titre'] ?? '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= $tachemodif['description'] ?? '' ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="en cours" <?= isset($tachemodif) && $tachemodif['statut'] == "en cours" ? "selected" : "" ?>>
                                En cours
                            </option>
                            <option value="terminée" <?= isset($tachemodif) && $tachemodif['statut'] == "terminée" ? "selected" : "" ?>>
                                Terminée
                            </option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <?= $tachemodif ? "Enregistrer" : "Ajouter" ?>
                    </button>

                    <?php if ($tachemodif): ?>
                        <a href="taches.php" class="btn btn-secondary">Annuler</a>
                    <?php endif; ?>

                </form>
            </div>
        </div>
    <?php endif; ?>
   <h2 class="mb-3">Liste des tâches</h2>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($taches as $tache): ?>
            <div class="col">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">

                        <?php if ($user['role'] == 'admin'): ?>
                            <p class="text-muted mb-1">
                                Utilisateur : <b><?= htmlspecialchars($tache['username']) ?></b>
                            </p>
                        <?php endif; ?>

                        <h5 class="card-title"><?= htmlspecialchars($tache['titre']) ?></h5>

                        <p class="card-text"><?= nl2br(htmlspecialchars($tache['description'])) ?></p>

                        <span class="badge bg-<?= $tache['statut'] == "terminée" ? "success" : "warning text-dark" ?>">
                            <?= $tache['statut'] ?>
                        </span>

                        <?php if ($user['role'] == 'admin' || $tache['user_id'] == $user['id']): ?>
                            <div class="mt-auto pt-3">
                                <a href="taches.php?modifier=<?= $tache['id'] ?>" class="btn btn-sm btn-primary">
                                    Modifier
                                </a>
                                <a href="actions.php?supprimer=<?= $tache['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Supprimer cette tâche ?');">
                                    Supprimer
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="card-footer text-muted text-end">
                        <?= $tache['date_creation'] ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
