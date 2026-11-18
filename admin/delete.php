<?php
/**
 * Admin Delete Registration Action
 * Event Registration System
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

init_session();
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($submittedToken)) {
        set_flash('danger', 'Security verification failed (invalid CSRF token). Please try again.');
        header("Location: " . BASE_URL . "admin/index.php");
        exit;
    }

    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        set_flash('danger', 'Invalid registration ID.');
        header("Location: " . BASE_URL . "admin/index.php");
        exit;
    }

    try {
        $pdo = getDBConnection();

        // Optional: fetch name for friendly flash message
        $stmtName = $pdo->prepare("SELECT name FROM `registrations` WHERE `id` = :id LIMIT 1");
        $stmtName->execute([':id' => $id]);
        $reg = $stmtName->fetch();
        $name = $reg ? $reg['name'] : "#$id";

        delete_registration($pdo, $id);
        set_flash('success', "Registration #{$id} for <strong>" . htmlspecialchars($name) . "</strong> was successfully deleted.");

    } catch (PDOException $e) {
        set_flash('danger', 'Database error while deleting registration: ' . $e->getMessage());
    }
} else {
    set_flash('warning', 'Direct deletion requests are not allowed.');
}

header("Location: " . BASE_URL . "admin/index.php");
exit;
