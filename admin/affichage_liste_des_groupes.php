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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des groupes</title>
    <link rel="stylesheet" href="../assets/css/admin-etudiant.css">
</head>
<body>
    <main class="student-page">
        <section class="student-card student-card--list">
            <header class="student-card__header student-list-header">
                <div>
                    <p class="eyebrow">Administration</p>
                    <h1>Liste des groupes</h1>
                    <p class="student-list-count">
                        <?= count($groupes) ?> groupe<?= count($groupes) === 1 ? '' : 's' ?>
                    </p>
                </div>
                <a class="button button--primary" href="creer_groupe.php">Créer un groupe</a>
            </header>

            <?php if (empty($groupes)): ?>
                <div class="student-list-empty">
                    <p>Aucun groupe n’est enregistré pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="student-table-wrap">
                    <table class="student-table">
                        <thead>
                            <tr>
                                <th scope="col">Code</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Filière</th>
                                <th scope="col">Année</th>
                                <th scope="col">Capacité</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupes as $groupe): ?>
                                <tr>
                                    <td><?= htmlspecialchars($groupe['code'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($groupe['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($groupe['filiere'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($groupe['annee'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($groupe['capacity'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <span class="student-status student-status--<?= strtolower(htmlspecialchars($groupe['statut'], ENT_QUOTES, 'UTF-8')) ?>">
                                            <?= htmlspecialchars($groupe['statut'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="student-actions">
                                            <a class="button button--secondary" href="modifier_groupe.php?id=<?= (int) $groupe['id'] ?>">Modifier</a>
                                            <a class="button button--primary" href="consulter_etudiants_groupe.php?id=<?= (int) $groupe['id'] ?>">Étudiants</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
