<?php
/**
 * Admin Logout Action
 * Event Registration System
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

init_session();

// Unset admin session variables
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_name']);

// Set notification and redirect
set_flash('info', 'You have been signed out safely.');
header("Location: " . BASE_URL . "admin/login.php");
exit;
