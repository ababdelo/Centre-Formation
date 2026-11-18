<?php

session_start();

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/Etudiant.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($id === false || $id === null) {
    http_response_code(400);
    exit('Identifiant etudiant invalide.');
}

$etudiantModel = new Etudiant($pdo);
$errors = [];
$success = false;

try {
    $etudiant = $etudiantModel->findById($id);

    if ($etudiant === null) {
        http_response_code(404);
        exit('Etudiant introuvable.');
    }

    $groupes = $pdo->query(
        'SELECT id, code, nom FROM groupe ORDER BY code'
    )->fetchAll();
} catch (PDOException $exception) {
    error_log('Unable to load student for editing: ' . $exception->getMessage());
    http_response_code(500);
    exit('Impossible de charger les informations de cet etudiant.');
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
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $errors[] = 'Session invalide. Veuillez reessayer.';
    } else {
        try {
            $etudiantModel->update($id, $form);
            $etudiant = $etudiantModel->findById($id);
            $success = true;
        } catch (InvalidArgumentException $exception) {
            $errors[] = $exception->getMessage();
        } catch (PDOException $exception) {
            error_log('Unable to update student: ' . $exception->getMessage());
            $errors[] = 'Cet email existe deja ou le groupe selectionne est invalide.';
        } catch (Throwable $exception) {
            error_log('Unable to update student: ' . $exception->getMessage());
            $errors[] = 'Une erreur est survenue lors de la modification.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier un etudiant</title>
    <link rel="stylesheet" href="../assets/css/admin-etudiant.css">
</head>
<body>
    <main class="student-page">
        <section class="student-card">
            <header class="student-card__header student-list-header">
                <div>
                    <p class="eyebrow">Administration</p>
                    <h1>Modifier les informations</h1>
                </div>
                <a class="button button--secondary" href="fiche-etudiant.php?id=<?= (int) $id ?>">Retour a la fiche</a>
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
                    Les informations de l'etudiant ont ete modifiees.
                </div>
            <?php endif; ?>

            <p class="student-matricule">
                Matricule :
                <strong><?= htmlspecialchars($etudiant->matricule ?? '—', ENT_QUOTES, 'UTF-8') ?></strong>
                <span>Le matricule est genere par le systeme et ne peut pas etre modifie.</span>
            </p>

            <form class="student-form" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-grid">
                    <label>
                        <span>Nom *</span>
                        <input type="text" name="nom" value="<?= htmlspecialchars($form['nom'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label>
                        <span>Prenom *</span>
                        <input type="text" name="prenom" value="<?= htmlspecialchars($form['prenom'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label>
                        <span>Email *</span>
                        <input type="email" name="email" value="<?= htmlspecialchars($form['email'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label>
                        <span>Telephone *</span>
                        <input type="tel" name="telephone" value="<?= htmlspecialchars($form['telephone'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label>
                        <span>Date de naissance *</span>
                        <input type="date" name="date_naissance" value="<?= htmlspecialchars($form['date_naissance'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label>
                        <span>Statut</span>
                        <select name="statut">
                            <?php foreach (['ACTIF', 'SUSPENDU', 'ABANDONNE', 'TERMINE'] as $status): ?>
                                <option value="<?= $status ?>" <?= $form['statut'] === $status ? 'selected' : '' ?>>
                                    <?= ucfirst(strtolower($status)) ?>
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
