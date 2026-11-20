<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/groupe.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $nom = $_POST['nom'];
    $filiere = $_POST['filiere'];
    $annee = $_POST['annee'];
    $capacity = $_POST['capacity'];
    $statut = $_POST['statut'];

    $groupe = new Groupe($pdo);
    $groupe->ajouter($code, $nom, $filiere, $annee, $capacity, $statut);

    $message = 'Groupe créé avec succès.';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Créer un groupe</title>
    <link rel="stylesheet" href="../assets/css/admin-etudiant.css">
</head>
<body>
    <main class="student-page">
        <section class="student-card">
            <header class="student-card__header student-list-header">
                <div>
                    <p class="eyebrow">Administration</p>
                    <h1>Créer un groupe</h1>
                </div>
                <a class="button button--secondary" href="affichage_liste_des_groupes.php">Liste des groupes</a>
            </header>

            <?php if ($message !== ''): ?>
                <div class="alert alert--success" role="status"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form class="student-form" method="post">
                <div class="form-grid">
                    <label><span>Code *</span><input type="text" name="code" required></label>
                    <label><span>Nom *</span><input type="text" name="nom" required></label>
                    <label><span>Filière *</span><input type="text" name="filiere" required></label>
                    <label><span>Année *</span><input type="number" name="annee" required></label>
                    <label><span>Capacité *</span><input type="number" name="capacity" required></label>
                    <label>
                        <span>Statut</span>
                        <select name="statut">
                            <option value="PLANIFIE">PLANIFIE</option>
                            <option value="EN_COURS">EN_COURS</option>
                            <option value="TERMINE">TERMINE</option>
                            <option value="ANNULE">ANNULE</option>
                        </select>
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="button button--primary">Créer le groupe</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
