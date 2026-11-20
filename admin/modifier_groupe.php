<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/groupe.php';

$groupe = new Groupe($pdo);
$id = $_GET['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupe->modifier(
        $_POST['id'],
        $_POST['code'],
        $_POST['nom'],
        $_POST['filiere'],
        $_POST['annee'],
        $_POST['capacity'],
        $_POST['statut']
    );

    $message = 'Groupe modifié avec succès.';
}

$data = $groupe->trouver($id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier un groupe</title>
    <link rel="stylesheet" href="../assets/css/admin-etudiant.css">
</head>
<body>
    <main class="student-page">
        <section class="student-card">
            <header class="student-card__header student-list-header">
                <div>
                    <p class="eyebrow">Administration</p>
                    <h1>Modifier un groupe</h1>
                </div>
                <a class="button button--secondary" href="affichage_liste_des_groupes.php">Liste des groupes</a>
            </header>

            <?php if ($message !== ''): ?>
                <div class="alert alert--success" role="status"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($data !== false): ?>
                <form class="student-form" method="post">
                    <input type="hidden" name="id" value="<?= (int) $data['id'] ?>">
                    <div class="form-grid">
                        <label><span>Code *</span><input type="text" name="code" value="<?= htmlspecialchars($data['code'], ENT_QUOTES, 'UTF-8') ?>" required></label>
                        <label><span>Nom *</span><input type="text" name="nom" value="<?= htmlspecialchars($data['nom'], ENT_QUOTES, 'UTF-8') ?>" required></label>
                        <label><span>Filière *</span><input type="text" name="filiere" value="<?= htmlspecialchars($data['filiere'], ENT_QUOTES, 'UTF-8') ?>" required></label>
                        <label><span>Année *</span><input type="number" name="annee" value="<?= htmlspecialchars($data['annee'], ENT_QUOTES, 'UTF-8') ?>" required></label>
                        <label><span>Capacité *</span><input type="number" name="capacity" value="<?= htmlspecialchars($data['capacity'], ENT_QUOTES, 'UTF-8') ?>" required></label>
                        <label>
                            <span>Statut</span>
                            <select name="statut">
                                <?php foreach (['PLANIFIE', 'EN_COURS', 'TERMINE', 'ANNULE'] as $statut): ?>
                                    <option value="<?= $statut ?>" <?= $data['statut'] === $statut ? 'selected' : '' ?>><?= $statut ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="button button--primary">Enregistrer les modifications</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert--error">Groupe introuvable.</div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
