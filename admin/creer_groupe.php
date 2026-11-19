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
    <title>Créer un groupe</title>
    <style>
        body { font-family: Arial; background: white; }
        form { width: 400px; margin: 40px; background: white; padding: 20px; }
        input, select { width: 100%; padding: 8px; margin: 5px 0 15px; }
        button { padding: 10px; background:blue; color: white; border: none; }
    </style>
</head>
<body>
    <form method="post">
        <h2>Créer un groupe</h2>
        <p><?= $message ?></p>

        <label>Code</label>
        <input type="text" name="code" required>

        <label>Nom</label>
        <input type="text" name="nom" required>

        <label>Filière</label>
        <input type="text" name="filiere" required>

        <label>Année</label>
        <input type="number" name="annee" required>

        <label>Capacité</label>
        <input type="number" name="capacity" required>

        <label>Statut</label>
        <select name="statut">
            <option value="PLANIFIE">PLANIFIE</option>
            <option value="EN_COURS">EN_COURS</option>
            <option value="TERMINE">TERMINE</option>
            <option value="ANNULE">ANNULE</option>
        </select>

        <button type="submit">Créer</button>
    </form>
</body>
</html>
