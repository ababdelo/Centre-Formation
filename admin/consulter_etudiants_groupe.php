<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/groupe.php';
require_once __DIR__ . '/../classes/etudiant.php';

$groupe = new Groupe($pdo);
$idGroupe = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);
$dataGroupe = false;

if (is_int($idGroupe)) {
    $dataGroupe = $groupe->trouver($idGroupe);
    if ($dataGroupe === false) {
        http_response_code(404);
    }
} else {
    http_response_code(400);
}

$etudiant = new Etudiant($pdo);
$message = '';
$etudiants = [];
$etudiantsSansGroupe = [];

if ($dataGroupe !== false && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter']) && isset($_POST['id_etudiant']) && $_POST['id_etudiant'] !== '') {
        $ajoute = $etudiant->ajouterAuGroupe((int) $_POST['id_etudiant'], (int) $idGroupe);
        $message = $ajoute
            ? 'Étudiant ajouté au groupe.'
            : 'Impossible d’ajouter cet étudiant : il est introuvable ou déjà affecté à un groupe.';
    }

    if (isset($_POST['retirer']) && isset($_POST['id_etudiant'])) {
        $retire = $etudiant->retirerDuGroupe((int) $_POST['id_etudiant']);
        $message = $retire
            ? 'Étudiant retiré du groupe.'
            : 'Impossible de retirer cet étudiant : il est introuvable ou sans groupe.';
    }
}

if ($dataGroupe !== false) {
    $etudiants = $etudiant->listerParGroupe($idGroupe);
    $etudiantsSansGroupe = $etudiant->listerSansGroupe();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Étudiants du groupe</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; }
        .liste { width: 900px; margin: 40px auto; background: white; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #0066cc; color: white; }
    </style>
</head>
<body>
    <div class="liste">
        <?php if ($dataGroupe === false): ?>
            <h2>Groupe introuvable</h2>
            <p role="alert">L’identifiant du groupe est manquant, invalide ou inexistant.</p>
            <a href="affichage_liste_des_groupes.php">Retour à la liste des groupes</a>
        <?php else: ?>
            <h2>Étudiants du groupe <?= htmlspecialchars($dataGroupe['nom'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>

            <form method="post">
                <label>Ajouter un étudiant :</label>
                <select name="id_etudiant">
                    <?php foreach ($etudiantsSansGroupe as $e): ?>
                        <option value="<?= (int) $e['id'] ?>">
                            <?= htmlspecialchars($e['nom'] . ' ' . $e['prenom'] . ' (' . ($e['matricule'] ?? '') . ')', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="ajouter">Ajouter</button>
            </form>

            <table>
                <tr>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>

                <?php foreach ($etudiants as $etudiant): ?>
                    <tr>
                        <td><?= htmlspecialchars($etudiant['matricule'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($etudiant['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($etudiant['prenom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($etudiant['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($etudiant['telephone'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($etudiant['statut'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="id_etudiant" value="<?= (int) $etudiant['id'] ?>">
                                <button type="submit" name="retirer">Retirer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
