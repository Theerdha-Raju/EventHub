<?php
/**
 * Helper Functions
 * Event Registration System
 */

// Start session safely
function init_session() {
    if (session_status() === PHP_SESSION_NONE) {
        // Enforce cookie security parameters
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }
}

// Sanitize string output
function sanitize($data) {
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

// Generate or retrieve CSRF token
function csrf_token() {
    init_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify submitted CSRF token
function verify_csrf_token($token) {
    init_session();
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Check if admin is currently authenticated
function is_admin_logged_in() {
    init_session();
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Restrict access to authenticated admins only
function require_admin() {
    if (!is_admin_logged_in()) {
        header("Location: " . BASE_URL . "admin/login.php");
        exit;
    }
}

// Set a flash notification message
function set_flash($type, $message) {
    init_session();
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

// Retrieve and clear flash notification
function get_flash() {
    init_session();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Format date and time for display
function format_event_date($datetimeStr) {
    if (!$datetimeStr) return 'TBA';
    $dt = new DateTime($datetimeStr);
    return $dt->format('D, M j, Y \a\t g:i A');
}

// Format short date
function format_short_date($datetimeStr) {
    if (!$datetimeStr) return 'N/A';
    $dt = new DateTime($datetimeStr);
    return $dt->format('M j, Y - g:i A');
}

// Fetch all upcoming events (ordered by event date)
function get_upcoming_events($pdo) {
    $stmt = $pdo->query("SELECT * FROM `events` ORDER BY `event_date` ASC");
    return $stmt->fetchAll();
}

// Fetch single event by ID
function get_event_by_id($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM `events` WHERE `id` = :id LIMIT 1");
    $stmt->execute([':id' => (int)$id]);
    return $stmt->fetch();
}

// Fetch all registrations with joined event details
function get_all_registrations($pdo) {
    $sql = "SELECT 
                r.id, 
                r.name, 
                r.email, 
                r.registration_date, 
                r.event_id,
                e.title AS event_title, 
                e.event_date, 
                e.location AS event_location,
                e.category AS event_category
            FROM `registrations` r
            JOIN `events` e ON r.event_id = e.id
            ORDER BY r.registration_date DESC, r.id DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// Fetch single registration by ID with event details
function get_registration_by_id($pdo, $id) {
    $sql = "SELECT 
                r.id, 
                r.name, 
                r.email, 
                r.registration_date, 
                r.event_id,
                e.title AS event_title, 
                e.description AS event_description,
                e.event_date, 
                e.location AS event_location,
                e.category AS event_category
            FROM `registrations` r
            JOIN `events` e ON r.event_id = e.id
            WHERE r.id = :id
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => (int)$id]);
    return $stmt->fetch();
}

// Delete a registration by ID
function delete_registration($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM `registrations` WHERE `id` = :id");
    return $stmt->execute([':id' => (int)$id]);
}

// Generate a secure verification hash for registration confirmation links
function generate_registration_token($registrationId, $email) {
    $secret = 'EVENT_SECRET_KEY_#99281_SECURE';
    return hash_hmac('sha256', $registrationId . ':' . strtolower(trim($email)), $secret);
}
