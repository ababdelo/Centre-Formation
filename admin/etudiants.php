<?php

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/Etudiant.php';

$error = null;
$etudiants = [];

try {
    $etudiants = (new Etudiant($pdo))->findAll();
} catch (PDOException $exception) {
    error_log('Unable to load students: ' . $exception->getMessage());
    $error = 'Impossible de charger la liste des etudiants.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des etudiants</title>
    <link rel="stylesheet" href="../assets/css/admin-etudiant.css">
</head>
<body>
    <main class="student-page">
        <section class="student-card student-card--list">
            <header class="student-card__header student-list-header">
                <div>
                    <p class="eyebrow">Administration</p>
                    <h1>Liste des etudiants</h1>
                    <p class="student-list-count">
                        <?= count($etudiants) ?> etudiant<?= count($etudiants) === 1 ? '' : 's' ?>
                    </p>
                </div>
                <a class="button button--primary" href="ajouter-etudiant.php">Ajouter un etudiant</a>
            </header>

            <?php if ($error !== null): ?>
                <div class="alert alert--error" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php elseif (empty($etudiants)): ?>
                <div class="student-list-empty">
                    <p>Aucun etudiant n'est enregistre pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="student-table-wrap">
                    <table class="student-table">
                        <thead>
                            <tr>
                                <th scope="col">Matricule</th>
                                <th scope="col">Nom et prenom</th>
                                <th scope="col">Email</th>
                                <th scope="col">Telephone</th>
                                <th scope="col">Date de naissance</th>
                                <th scope="col">Groupe</th>
                                <th scope="col">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($etudiants as $etudiant): ?>
                                <tr>
                                    <td>
                                        <a href="fiche-etudiant.php?id=<?= (int) $etudiant->id ?>">
                                            <?= htmlspecialchars($etudiant->matricule ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($etudiant->nom, ENT_QUOTES, 'UTF-8') ?>
                                        <?= htmlspecialchars($etudiant->prenom, ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars($etudiant->email, ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($etudiant->email, ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($etudiant->telephone, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($etudiant->date_naissance, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?= htmlspecialchars($etudiant->groupe_code ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td>
                                        <span class="student-status student-status--<?= strtolower(htmlspecialchars($etudiant->statut, ENT_QUOTES, 'UTF-8')) ?>">
                                            <?= htmlspecialchars($etudiant->statut, ENT_QUOTES, 'UTF-8') ?>
                                        </span>
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
