<?php

session_start();

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../classes/Etudiant.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Methode non autorisee.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($id === false || $id === null) {
    http_response_code(400);
    exit('Identifiant etudiant invalide.');
}

if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !is_string($_POST['csrf_token']) ||
    !is_string($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    $_SESSION['student_flash'] = [
        'type' => 'error',
        'message' => 'Session invalide. Veuillez reessayer.',
    ];
} else {
    try {
        $updated = (new Etudiant($pdo))->desactiver($id);

        $_SESSION['student_flash'] = $updated
            ? [
                'type' => 'success',
                'message' => 'Etudiant desactive avec succes.',
            ]
            : [
                'type' => 'error',
                'message' => 'Etudiant introuvable.',
            ];
    } catch (InvalidArgumentException $exception) {
        $_SESSION['student_flash'] = [
            'type' => 'error',
            'message' => $exception->getMessage(),
        ];
    } catch (PDOException $exception) {
        error_log('Unable to deactivate student: ' . $exception->getMessage());
        $_SESSION['student_flash'] = [
            'type' => 'error',
            'message' => 'Impossible de desactiver cet etudiant.',
        ];
    } catch (Throwable $exception) {
        error_log('Unable to deactivate student: ' . $exception->getMessage());
        $_SESSION['student_flash'] = [
            'type' => 'error',
            'message' => 'Une erreur est survenue lors de la desactivation.',
        ];
    }
}

header('Location: fiche-etudiant.php?id=' . $id, true, 303);
exit;
