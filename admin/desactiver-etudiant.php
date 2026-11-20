<?php

session_start();

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/etudiant.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Méthode non autorisée.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($id === false || $id === null) {
    http_response_code(400);
    exit('Identifiant étudiant invalide.');
}

if (!isset($_POST['csrf_token'], $_SESSION['csrf_token'])
    || !is_string($_POST['csrf_token'])
    || !is_string($_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['student_flash'] = [
        'type' => 'error',
        'message' => 'La session a expiré. Réessayez.',
    ];
} else {
    try {
        if ((new Etudiant($pdo))->desactiver($id)) {
            $_SESSION['student_flash'] = [
                'type' => 'success',
                'message' => 'L’étudiant a été désactivé.',
            ];
        } else {
            $_SESSION['student_flash'] = [
                'type' => 'error',
                'message' => 'Étudiant introuvable.',
            ];
        }
    } catch (InvalidArgumentException $exception) {
        $_SESSION['student_flash'] = [
            'type' => 'error',
            'message' => $exception->getMessage(),
        ];
    } catch (PDOException $exception) {
        error_log('Erreur lors de la désactivation de l’étudiant : ' . $exception->getMessage());
        $_SESSION['student_flash'] = [
            'type' => 'error',
            'message' => 'La désactivation a échoué. Réessayez plus tard.',
        ];
    }
}

header('Location: fiche-etudiant.php?id=' . $id, true, 303);
exit;
