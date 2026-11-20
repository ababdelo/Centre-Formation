<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/groupe.php';

$groupe = new Groupe($pdo);
$groupes = $groupe->lister();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des groupes</title>
    <style>
        body { font-family: Arial; background:white; }
        .liste { width: 900px; margin: 40px auto; background: white; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid white; padding: 8px; text-align: left; }
        th { background:blue; color: white; }
        a { color:blue; }
    </style>
</head>
<body>
    <div class="liste">
        <h2>Liste des groupes</h2>

        <table>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Filière</th>
                <th>Année</th>
                <th>Capacité</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>

            <?php foreach ($groupes as $groupe): ?>
                <tr>
                    <td><?= $groupe['code'] ?></td>
                    <td><?= $groupe['nom'] ?></td>
                    <td><?= $groupe['filiere'] ?></td>
                    <td><?= $groupe['annee'] ?></td>
                    <td><?= $groupe['capacity'] ?></td>
                    <td><?= $groupe['statut'] ?></td>
                    <td>
                        <a href="modifier_groupe.php?id=<?= $groupe['id'] ?>">Modifier</a>
                        |
                        <a href="consulter_etudiants_groupe.php?id=<?= $groupe['id'] ?>">Étudiants</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
