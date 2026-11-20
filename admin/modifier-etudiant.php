<?php

session_start();

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/etudiant.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($id === false || $id === null) {
    http_response_code(400);
    exit('Identifiant étudiant invalide.');
}

$etudiantModel = new Etudiant($pdo);
$errors = [];
$success = false;

try {
    $etudiant = $etudiantModel->findById($id);

    if ($etudiant === null) {
        http_response_code(404);
        exit('Étudiant introuvable.');
    }

    $groupes = $pdo->query(
        'SELECT id, code, nom FROM groupe ORDER BY code'
    )->fetchAll();
} catch (PDOException $exception) {
    error_log('Erreur lors du chargement de l’étudiant à modifier : ' . $exception->getMessage());
    http_response_code(500);
    exit('Impossible de charger les informations de cet étudiant.');
}

$form = [
    'nom' => $etudiant->nom,
    'prenom' => $etudiant->prenom,
    'email' => $etudiant->email,
    'telephone' => $etudiant->telephone,
    'date_naissance' => $etudiant->date_naissance,
    'statut' => $etudiant->statut,
    'id_groupe' => $etudiant->id_groupe ?? '',
];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = [
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'date_naissance' => $_POST['date_naissance'] ?? '',
        'statut' => $_POST['statut'] ?? 'ACTIF',
        'id_groupe' => $_POST['id_groupe'] ?? '',
    ];

    if (
        !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
        !is_string($_POST['csrf_token']) ||
        !is_string($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $errors[] = 'La session a expiré. Veuillez réessayer.';
    } else {
        try {
            $etudiantModel->update($id, $form);
            $etudiant = $etudiantModel->findById($id);
            $success = true;
        } catch (InvalidArgumentException $exception) {
            $errors[] = $exception->getMessage();
        } catch (PDOException $exception) {
            error_log('Erreur lors de la modification de l’étudiant : ' . $exception->getMessage());
            $errors[] = 'Impossible d’enregistrer les modifications. Vérifiez l’adresse e-mail et le groupe choisi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier un étudiant</title>
    <link rel="stylesheet" href="../assets/css/admin-etudiant.css">
</head>
<body>
    <main class="student-page">
        <section class="student-card">
            <header class="student-card__header student-list-header">
                <div>
                    <p class="eyebrow">Administration</p>
                    <h1>Modifier les informations de l’étudiant</h1>
                </div>
                <a class="button button--secondary" href="fiche-etudiant.php?id=<?= (int) $id ?>">Retour à la fiche</a>
            </header>

            <?php if (!empty($errors)): ?>
                <div class="alert alert--error" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert--success" role="status">
                    Les informations de l’étudiant ont été modifiées.
                </div>
            <?php endif; ?>

            <p class="student-matricule">
                Matricule :
                <strong><?= htmlspecialchars($etudiant->matricule ?? '—', ENT_QUOTES, 'UTF-8') ?></strong>
                <span>Le matricule est généré automatiquement et ne peut pas être modifié.</span>
            </p>

            <form class="student-form" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-grid">
                    <label>
                        <span>Nom *</span>
                        <input type="text" name="nom" value="<?= htmlspecialchars($form['nom'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label>
                        <span>Prénom *</span>
                        <input type="text" name="prenom" value="<?= htmlspecialchars($form['prenom'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label>
                        <span>Email *</span>
                        <input type="email" name="email" value="<?= htmlspecialchars($form['email'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label>
                        <span>Téléphone *</span>
                        <input type="tel" name="telephone" value="<?= htmlspecialchars($form['telephone'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label>
                        <span>Date de naissance *</span>
                        <input type="date" name="date_naissance" value="<?= htmlspecialchars($form['date_naissance'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label>
                        <span>Statut</span>
                        <select name="statut">
                            <?php foreach ([
                                'ACTIF' => 'Actif',
                                'SUSPENDU' => 'Suspendu',
                                'ABANDONNE' => 'Abandonné',
                                'TERMINE' => 'Terminé',
                            ] as $status => $label): ?>
                                <option value="<?= $status ?>" <?= $form['statut'] === $status ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="form-grid__full">
                        <span>Groupe</span>
                        <select name="id_groupe">
                            <option value="">Aucun groupe</option>
                            <?php foreach ($groupes as $groupe): ?>
                                <option value="<?= (int) $groupe->id ?>" <?= (string) $form['id_groupe'] === (string) $groupe->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($groupe->code . ' - ' . $groupe->nom, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="button button--primary">Enregistrer les modifications</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
