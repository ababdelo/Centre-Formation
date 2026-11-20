<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/groupe.php';
require_once __DIR__ . '/../classes/etudiant.php';

$idGroupe = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($idGroupe === false || $idGroupe === null) {
    http_response_code(400);
    exit('Identifiant groupe invalide.');
}

$groupe = new Groupe($pdo);
$dataGroupe = $groupe->trouver($idGroupe);

$etudiant = new Etudiant($pdo);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter']) && $_POST['id_etudiant'] !== '') {
        $etudiant->ajouterAuGroupe($_POST['id_etudiant'], $idGroupe);
        $message = 'Étudiant ajouté au groupe.';
    }

    if (isset($_POST['retirer'])) {
        $etudiant->retirerDuGroupe($_POST['id_etudiant']);
        $message = 'Étudiant retiré du groupe.';
    }
}

$etudiants = $etudiant->listerParGroupe($idGroupe);
$etudiantsSansGroupe = $etudiant->listerSansGroupe();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Étudiants du groupe</title>
    <link rel="stylesheet" href="../assets/css/admin-etudiant.css">
</head>
<body>
    <main class="student-page">
        <section class="student-card student-card--list">
            <header class="student-card__header student-list-header">
                <div>
                    <p class="eyebrow">Administration</p>
                    <h1>Étudiants du groupe <?= htmlspecialchars($dataGroupe['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
                    <p class="student-list-count"><?= count($etudiants) ?> étudiant<?= count($etudiants) === 1 ? '' : 's' ?></p>
                </div>
                <a class="button button--secondary" href="affichage_liste_des_groupes.php">Liste des groupes</a>
            </header>

            <?php if ($message !== ''): ?>
                <div class="alert alert--success" role="status"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if (!empty($etudiantsSansGroupe)): ?>
                <form class="student-form" method="post">
                    <div class="form-grid">
                        <label class="form-grid__full">
                            <span>Ajouter un étudiant sans groupe</span>
                            <select name="id_etudiant">
                                <?php foreach ($etudiantsSansGroupe as $e): ?>
                                    <option value="<?= (int) $e['id'] ?>">
                                        <?= htmlspecialchars($e['nom'] . ' ' . $e['prenom'] . ' (' . $e['matricule'] . ')', ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="ajouter" class="button button--primary">Ajouter au groupe</button>
                    </div>
                </form>
            <?php endif; ?>

            <?php if (empty($etudiants)): ?>
                <div class="student-list-empty">
                    <p>Aucun étudiant n’est affecté à ce groupe.</p>
                </div>
            <?php else: ?>
                <div class="student-table-wrap">
                    <table class="student-table">
                        <thead>
                            <tr>
                                <th scope="col">Matricule</th>
                                <th scope="col">Nom et prénom</th>
                                <th scope="col">Email</th>
                                <th scope="col">Téléphone</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($etudiants as $etudiant): ?>
                                <tr>
                                    <td><?= htmlspecialchars($etudiant['matricule'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><a href="mailto:<?= htmlspecialchars($etudiant['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($etudiant['email'], ENT_QUOTES, 'UTF-8') ?></a></td>
                                    <td><?= htmlspecialchars($etudiant['telephone'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <span class="student-status student-status--<?= strtolower(htmlspecialchars($etudiant['statut'], ENT_QUOTES, 'UTF-8')) ?>">
                                            <?= htmlspecialchars($etudiant['statut'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="post" class="student-actions">
                                            <input type="hidden" name="id_etudiant" value="<?= (int) $etudiant['id'] ?>">
                                            <button type="submit" name="retirer" class="button button--danger">Retirer</button>
                                        </form>
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
