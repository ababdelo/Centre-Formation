<?php

session_start();

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/etudiant.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$flash = $_SESSION['student_flash'] ?? null;
unset($_SESSION['student_flash']);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);
$etudiant = null;
$error = null;

if ($id === false || $id === null) {
    http_response_code(400);
    $error = 'Identifiant étudiant invalide.';
} else {
    try {
        $etudiant = (new Etudiant($pdo))->findById($id);

        if ($etudiant === null) {
            http_response_code(404);
            $error = 'Étudiant introuvable.';
        }
    } catch (PDOException $exception) {
        error_log('Erreur lors du chargement de la fiche étudiant : ' . $exception->getMessage());
        http_response_code(500);
        $error = 'Impossible de charger la fiche de cet étudiant.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fiche étudiant</title>
    <link rel="stylesheet" href="../assets/css/admin-etudiant.css">
</head>
<body>
    <main class="student-page">
        <section class="student-card">
            <header class="student-card__header student-list-header">
                <div>
                    <p class="eyebrow">Administration</p>
                    <h1>Fiche de l’étudiant</h1>
                </div>
                <div class="student-actions">
                    <?php if ($etudiant !== null): ?>
                        <a class="button button--primary" href="modifier-etudiant.php?id=<?= (int) $etudiant->id ?>">Modifier</a>
                        <?php if ($etudiant->statut === 'ACTIF'): ?>
                            <form method="post" action="desactiver-etudiant.php" onsubmit="return confirm('Désactiver cet étudiant ?');">
                                <input type="hidden" name="id" value="<?= (int) $etudiant->id ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                <button class="button button--danger" type="submit">Désactiver</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                    <a class="button button--secondary" href="etudiants.php">Retour à la liste</a>
                </div>
            </header>

            <?php if ($flash !== null): ?>
                <div class="alert alert--<?= $flash['type'] === 'success' ? 'success' : 'error' ?>" role="status">
                    <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if ($error !== null): ?>
                <div class="alert alert--error" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php elseif ($etudiant !== null): ?>
                <div class="student-profile">
                    <div class="student-profile__heading">
                        <h2>
                            <?= htmlspecialchars($etudiant->prenom, ENT_QUOTES, 'UTF-8') ?>
                            <?= htmlspecialchars($etudiant->nom, ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                        <span class="student-status student-status--<?= strtolower(htmlspecialchars($etudiant->statut, ENT_QUOTES, 'UTF-8')) ?>">
                            <?= htmlspecialchars($etudiant->statut, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>

                    <dl class="student-details">
                        <div>
                            <dt>Matricule</dt>
                            <dd><?= htmlspecialchars($etudiant->matricule ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div>
                            <dt>Email</dt>
                            <dd>
                                <a href="mailto:<?= htmlspecialchars($etudiant->email, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($etudiant->email, ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt>Téléphone</dt>
                            <dd><?= htmlspecialchars($etudiant->telephone, ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div>
                            <dt>Date de naissance</dt>
                            <dd><?= htmlspecialchars($etudiant->date_naissance, ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div>
                            <dt>Groupe</dt>
                            <dd>
                                <?php if ($etudiant->id_groupe !== null): ?>
                                    <?= htmlspecialchars($etudiant->groupe_code ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    - <?= htmlspecialchars($etudiant->groupe_nom ?? '', ENT_QUOTES, 'UTF-8') ?>
                                <?php else: ?>
                                    Aucun groupe affecté
                                <?php endif; ?>
                            </dd>
                        </div>
                    </dl>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
