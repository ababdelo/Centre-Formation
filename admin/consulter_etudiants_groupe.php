<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/groupe.php';
require_once __DIR__ . '/../classes/etudiant.php';

$idGroupe = $_GET['id'];

$groupe = new Groupe($pdo);
$dataGroupe = $groupe->trouver($idGroupe);

$etudiant = new Etudiant($pdo);
$etudiants = $etudiant->listerParGroupe($idGroupe);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Étudiants du groupe</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; }
        .liste { width: 900px; margin: 40px auto; background: white; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #0066cc; color: white; }
    </style>
</head>
<body>
    <div class="liste">
        <h2>Étudiants du groupe <?= $dataGroupe['nom'] ?></h2>

        <table>
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Statut</th>
            </tr>

            <?php foreach ($etudiants as $etudiant): ?>
                <tr>
                    <td><?= $etudiant['matricule'] ?></td>
                    <td><?= $etudiant['nom'] ?></td>
                    <td><?= $etudiant['prenom'] ?></td>
                    <td><?= $etudiant['email'] ?></td>
                    <td><?= $etudiant['telephone'] ?></td>
                    <td><?= $etudiant['statut'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
