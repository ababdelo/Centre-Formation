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
    <title>Modifier un groupe</title>
    <style>
        body { font-family: Arial; background: white; }
        form { width: 400px; margin: 40px auto; background: white; padding: 20px; }
        input, select { width: 100%; padding: 8px; margin: 5px 0 15px; }
        button { padding: 10px; background:blue; color: white; border: none; }
    </style>
</head>
<body>
    <form method="post">
        <h2>Modifier un groupe</h2>
        <p><?= $message ?></p>

        <input type="hidden" name="id" value="<?= $data['id'] ?>">

        <label>Code</label>
        <input type="text" name="code" value="<?= $data['code'] ?>" required>

        <label>Nom</label>
        <input type="text" name="nom" value="<?= $data['nom'] ?>" required>

        <label>Filière</label>
        <input type="text" name="filiere" value="<?= $data['filiere'] ?>" required>

        <label>Année</label>
        <input type="number" name="annee" value="<?= $data['annee'] ?>" required>

        <label>Capacité</label>
        <input type="number" name="capacity" value="<?= $data['capacity'] ?>" required>

        <label>Statut</label>
        <select name="statut">
            <?php foreach (['PLANIFIE', 'EN_COURS', 'TERMINE', 'ANNULE'] as $statut): ?>
                <option value="<?= $statut ?>" <?= $data['statut'] === $statut ? 'selected' : '' ?>>
                    <?= $statut ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Modifier</button>
    </form>
</body>
</html>
