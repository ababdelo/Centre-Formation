<?php

session_start();

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/Etudiant.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = null;
$form = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'telephone' => '',
    'date_naissance' => '',
    'statut' => 'ACTIF',
    'id_groupe' => '',
];

try {
    $groupes = $pdo->query(
        'SELECT id, code, nom FROM groupe ORDER BY code'
    )->fetchAll();
} catch (PDOException $exception) {
    error_log('Erreur lors du chargement des groupes : ' . $exception->getMessage());
    $groupes = [];
    $errors[] = 'Impossible de charger la liste des groupes.';
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
        $etudiant = new Etudiant($pdo);

        try {
            $id = $etudiant->create($form);
            $createdStudent = $etudiant->findById($id);

            $success = [
                'nom' => $createdStudent->nom,
                'prenom' => $createdStudent->prenom,
                'matricule' => $createdStudent->matricule,
            ];

            $form = [
                'nom' => '',
                'prenom' => '',
                'email' => '',
                'telephone' => '',
                'date_naissance' => '',
                'statut' => 'ACTIF',
                'id_groupe' => '',
            ];
        } catch (InvalidArgumentException $exception) {
            $errors[] = $exception->getMessage();
        } catch (PDOException $exception) {
            error_log('Erreur lors de l’ajout de l’étudiant : ' . $exception->getMessage());
            $errors[] = 'Impossible d’ajouter l’étudiant pour le moment. Réessayez plus tard.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter un étudiant</title>
    <link rel="stylesheet" href="../assets/css/admin-etudiant.css">
</head>
<body>
    <main class="student-page">
        <section class="student-card">
            <header class="student-card__header">
                <div>
                    <p class="eyebrow">Administration</p>
                    <h1>Ajouter un nouvel étudiant</h1>
                </div>
                <a class="button button--secondary" href="etudiants.php">Liste des étudiants</a>
            </header>

            <?php if (!empty($errors)): ?>
                <div class="alert alert--error" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success !== null): ?>
                <div class="alert alert--success" role="status">
                    <p>
                        <?= htmlspecialchars($success['prenom'], ENT_QUOTES, 'UTF-8') ?>
                        <?= htmlspecialchars($success['nom'], ENT_QUOTES, 'UTF-8') ?>
                        a été ajouté avec succès.
                    </p>
                    <p>
                        Matricule généré :
                        <strong><?= htmlspecialchars($success['matricule'], ENT_QUOTES, 'UTF-8') ?></strong>
                    </p>
                </div>
            <?php endif; ?>

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
                    <button type="submit" class="button button--primary">Ajouter l’étudiant</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
