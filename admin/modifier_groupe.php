<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/groupe.php';

$groupe = new Groupe($pdo);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);
$message = '';
$data = false;

if (is_int($id)) {
    $data = $groupe->trouver($id);
    if ($data === false) {
        http_response_code(404);
    }
} else {
    http_response_code(400);
}

if ($data !== false && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $filiere = trim($_POST['filiere'] ?? '');
    $annee = filter_var($_POST['annee'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);
    $capacity = filter_var($_POST['capacity'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);
    $statut = $_POST['statut'] ?? '';

    if (
        $code === '' ||
        $nom === '' ||
        $filiere === '' ||
        $annee === false ||
        $capacity === false ||
        !in_array($statut, ['PLANIFIE', 'EN_COURS', 'TERMINE', 'ANNULE'], true)
    ) {
        $message = 'Veuillez renseigner des informations valides.';
    } else {
        $groupe->modifier($id, $code, $nom, $filiere, $annee, $capacity, $statut);
        $data = $groupe->trouver($id);
        $message = 'Groupe modifié avec succès.';
    }
}
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
    <form method="post" action="modifier_groupe.php?id=<?= (int) $id ?>">
        <?php if ($data === false): ?>
            <h2>Groupe introuvable</h2>
            <p role="alert">L’identifiant du groupe est manquant, invalide ou inexistant.</p>
            <a href="affichage_liste_des_groupes.php">Retour à la liste des groupes</a>
        <?php else: ?>
            <h2>Modifier un groupe</h2>
            <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>

            <label for="code">Code</label>
            <input id="code" type="text" name="code" value="<?= htmlspecialchars($data['code'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label for="nom">Nom</label>
            <input id="nom" type="text" name="nom" value="<?= htmlspecialchars($data['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label for="filiere">Filière</label>
            <input id="filiere" type="text" name="filiere" value="<?= htmlspecialchars($data['filiere'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label for="annee">Année</label>
            <input id="annee" type="number" name="annee" value="<?= (int) $data['annee'] ?>" min="1" required>

            <label for="capacity">Capacité</label>
            <input id="capacity" type="number" name="capacity" value="<?= (int) $data['capacity'] ?>" min="1" required>

            <label for="statut">Statut</label>
            <select id="statut" name="statut">
                <?php foreach (['PLANIFIE', 'EN_COURS', 'TERMINE', 'ANNULE'] as $optionStatut): ?>
                    <option value="<?= $optionStatut ?>" <?= $data['statut'] === $optionStatut ? 'selected' : '' ?>>
                        <?= $optionStatut ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Modifier</button>
        <?php endif; ?>
    </form>
</body>
</html>
