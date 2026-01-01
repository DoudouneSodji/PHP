<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];  


if (isset($_POST['action']) && $_POST['action'] === 'ajouter') {

    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $statut = $_POST['statut'];
    $user_id = $user['id'];

    $sql = $pdo->prepare(
        "INSERT INTO taches (titre, description, statut, user_id, date_creation)
         VALUES (?, ?, ?, ?, NOW())"
    );
    $sql->execute([$titre, $description, $statut, $user_id]);

    header("Location: taches.php");
    exit;
}


if (isset($_POST['action']) && $_POST['action'] === 'modifier') {

    $id = $_POST['id'];
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $statut = $_POST['statut'];

    if ($user['role'] == 'admin') {
       
        $sql = $pdo->prepare(
            "UPDATE taches SET titre=?, description=?, statut=? WHERE id=?"
        );
        $sql->execute([$titre, $description, $statut, $id]);
    } else {
        
        $sql = $pdo->prepare(
            "UPDATE taches SET titre=?, description=?, statut=? WHERE id=? AND user_id=?"
        );
        $sql->execute([$titre, $description, $statut, $id, $user['id']]);
    }

    header("Location: taches.php");
    exit;
}


if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];

    if ($user['role'] == 'admin') {
       
        $sql = $pdo->prepare("DELETE FROM taches WHERE id=?");
        $sql->execute([$id]);
    } else {
        
        $sql = $pdo->prepare("DELETE FROM taches WHERE id=? AND user_id=?");
        $sql->execute([$id, $user['id']]);
    }

    header("Location: taches.php");
    exit;
}
