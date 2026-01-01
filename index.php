<?php
session_start();
require 'db.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = sha1($_POST['password']); 

    $sql = $pdo->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $sql->execute([$username, $password]);

    if ($sql->rowCount() == 1) {
        $_SESSION['user'] = $sql->fetch();
        header("Location: taches.php");
        exit;
    } else {
        $erreur = "Login ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>

   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-5 col-lg-4">

            <div class="card shadow">
                <div class="card-header text-center bg-primary text-white">
                    <h4 class="mb-0">Connexion</h4>
                </div>

                <div class="card-body">

                    <?php if (isset($erreur)): ?>
                        <div class="alert alert-danger text-center">
                            <?= $erreur ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nom d'utilisateur</label>
                            <input type="text" name="username" class="form-control" placeholder="Entrez votre login" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" placeholder="Entrez votre mot de passe" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="login" class="btn btn-primary">
                                Se connecter
                            </button>
                        </div>
                    </form>

                </div>

                
            </div>

        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
