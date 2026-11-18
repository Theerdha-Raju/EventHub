<?php
/**
 * Global Header Component
 * Event Registration System
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

init_session();
$flash = get_flash();
$isAdmin = is_admin_logged_in();
$title = isset($pageTitle) ? $pageTitle . ' - EventHub' : 'EventHub - Event Registration & Management';
$active = $activeNav ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL) ?>assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
    <!-- Top Navigation Bar -->
    <header class="navbar no-print">
        <div class="container">
            <div class="nav-wrapper">
                <a href="<?= htmlspecialchars(BASE_URL) ?>index.php" class="brand-logo">
                    <span class="brand-icon">🎟️</span>
                    <span>EventHub</span>
                </a>
                <nav>
                    <ul class="nav-links">
                        <li>
                            <a href="<?= htmlspecialchars(BASE_URL) ?>index.php" class="nav-link <?= $active === 'events' ? 'active' : '' ?>">
                                📅 Upcoming Events
                            </a>
                        </li>
                        <li>
                            <a href="<?= htmlspecialchars(BASE_URL) ?>register.php" class="nav-link <?= $active === 'register' ? 'active' : '' ?>">
                                ✍️ Register
                            </a>
                        </li>
                        <?php if ($isAdmin): ?>
                            <li>
                                <a href="<?= htmlspecialchars(BASE_URL) ?>admin/index.php" class="nav-link <?= $active === 'admin' ? 'active' : '' ?>">
                                    <span class="admin-badge-nav">🛡️ Admin Panel</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= htmlspecialchars(BASE_URL) ?>admin/logout.php" class="btn btn-secondary btn-sm" style="color: var(--danger);">
                                    Logout
                                </a>
                            </li>
                        <?php else: ?>
                            <li>
                                <a href="<?= htmlspecialchars(BASE_URL) ?>admin/login.php" class="nav-link <?= $active === 'login' ? 'active' : '' ?>">
                                    🔒 Admin Login
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Container Start -->
    <main class="main-content">
        <div class="container">
            <?php if ($flash): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> no-print">
                    <span><?= $flash['type'] === 'success' ? '✅' : ($flash['type'] === 'danger' ? '⚠️' : 'ℹ️') ?></span>
                    <div><?= $flash['message'] ?></div>
                </div>
            <?php endif; ?>
