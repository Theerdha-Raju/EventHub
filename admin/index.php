<?php
/**
 * Admin Dashboard - Registrations Management Table
 * Event Registration System
 */

$pageTitle = 'Admin Dashboard - Registrations';
$activeNav = 'admin';

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

init_session();
require_admin();

$pdo = getDBConnection();
$registrations = get_all_registrations($pdo);

// Dashboard KPI metrics
$totalRegistrations = count($registrations);
$uniqueEmails = count(array_unique(array_column($registrations, 'email')));
$stmtEventsCount = $pdo->query("SELECT COUNT(*) FROM `events`");
$totalEvents = (int)$stmtEventsCount->fetchColumn();

// Extra JS script for admin features
$extraScripts = '<script src="' . htmlspecialchars(BASE_URL) . 'assets/js/admin.js"></script>';

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Admin Dashboard Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <div style="font-size: 13px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px;">
            Administration Panel
        </div>
        <h1 style="font-size: 26px; font-weight: 800; color: var(--secondary); margin: 4px 0 0;">
            Event Registrations
        </h1>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <span style="font-size: 14px; color: var(--text-muted);">
            Logged in as <strong><?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin') ?></strong>
        </span>
        <a href="<?= htmlspecialchars(BASE_URL) ?>admin/logout.php" class="btn btn-outline-danger btn-sm">
            Sign Out
        </a>
    </div>
</div>

<!-- KPI Stat Cards -->
<div class="stat-cards-grid">
    <div class="stat-card">
        <div class="stat-icon-wrapper" style="background: var(--primary-light); color: var(--primary);">
            📋
        </div>
        <div>
            <div class="stat-val"><?= $totalRegistrations ?></div>
            <div class="stat-lbl">Total Registrations</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper" style="background: #e0f2fe; color: #0284c7;">
            🎯
        </div>
        <div>
            <div class="stat-val"><?= $totalEvents ?></div>
            <div class="stat-lbl">Active Events</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper" style="background: #dcfce7; color: #16a34a;">
            👥
        </div>
        <div>
            <div class="stat-val"><?= $uniqueEmails ?></div>
            <div class="stat-lbl">Unique Attendees</div>
        </div>
    </div>
</div>

<!-- Registrations Management Card -->
<div class="card" style="padding: 24px;">
    <div class="section-header" style="margin-bottom: 20px;">
        <div class="section-title" style="font-size: 18px;">
            <span>All Registered Attendees</span>
        </div>
        <!-- Table Search Bar -->
        <div class="search-box-wrapper">
            <span class="search-icon">🔍</span>
            <input 
                type="text" 
                id="adminTableSearch" 
                class="search-input" 
                placeholder="Filter by name, email, or event..." 
                autocomplete="off"
            >
        </div>
    </div>

    <?php if (!empty($registrations)): ?>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Event</th>
                        <th>Date Registered</th>
                        <th style="width: 170px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody id="registrationsTableBody">
                    <?php foreach ($registrations as $reg): ?>
                        <tr class="registration-row">
                            <!-- Column 1: ID -->
                            <td>
                                <strong style="color: var(--secondary);">#<?= (int)$reg['id'] ?></strong>
                            </td>

                            <!-- Column 2: Name -->
                            <td>
                                <span style="font-weight: 600; color: var(--secondary);">
                                    <?= htmlspecialchars($reg['name']) ?>
                                </span>
                            </td>

                            <!-- Column 3: Email -->
                            <td>
                                <a href="mailto:<?= htmlspecialchars($reg['email']) ?>" style="color: var(--primary);">
                                    <?= htmlspecialchars($reg['email']) ?>
                                </a>
                            </td>

                            <!-- Column 4: Event -->
                            <td>
                                <span class="table-badge" style="background: var(--primary-light); color: var(--primary); font-weight: 600;">
                                    <?= htmlspecialchars($reg['event_title']) ?>
                                </span>
                            </td>

                            <!-- Column 5: Date Registered -->
                            <td>
                                <span style="color: var(--text-muted); font-size: 13px;">
                                    <?= format_short_date($reg['registration_date']) ?>
                                </span>
                            </td>

                            <!-- Column 6: Action (View, delete) -->
                            <td>
                                <div class="action-buttons" style="justify-content: center;">
                                    <!-- View Action -->
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-primary btn-sm btn-view-reg" 
                                        data-id="<?= (int)$reg['id'] ?>"
                                        data-name="<?= htmlspecialchars($reg['name']) ?>"
                                        data-email="<?= htmlspecialchars($reg['email']) ?>"
                                        data-event="<?= htmlspecialchars($reg['event_title']) ?>"
                                        data-date="<?= format_short_date($reg['registration_date']) ?>"
                                        data-event-date="<?= format_event_date($reg['event_date']) ?>"
                                        data-event-location="<?= htmlspecialchars($reg['event_location']) ?>"
                                        title="View Details"
                                    >
                                        👁️ View
                                    </button>

                                    <!-- Delete Action -->
                                    <form 
                                        method="POST" 
                                        action="delete.php" 
                                        class="form-delete-reg" 
                                        data-id="<?= (int)$reg['id'] ?>" 
                                        data-name="<?= htmlspecialchars($reg['name']) ?>"
                                        style="margin: 0; display: inline-block;"
                                    >
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="id" value="<?= (int)$reg['id'] ?>">
                                        <button 
                                            type="submit" 
                                            class="btn btn-outline-danger btn-sm" 
                                            title="Delete Registration"
                                        >
                                            🗑️ Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Dynamic Row shown when search filter matches nothing -->
                    <tr id="noTableMatchesRow" style="display: none;">
                        <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">
                            No registrations match your search keyword.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📝</div>
            <h3 class="empty-title">No Registrations Found</h3>
            <p class="empty-desc">No users have registered for any event yet. Registrations will appear in this table once submitted.</p>
            <a href="<?= htmlspecialchars(BASE_URL) ?>register.php" class="btn btn-primary btn-sm">
                Register a Test Attendee
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Registration Details Modal -->
<div class="modal-overlay" id="viewModalOverlay">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title">
                Registration Summary <span id="modalRegId" style="color: var(--primary); font-family: monospace;"></span>
            </h3>
            <button type="button" class="modal-close modal-close-trigger" aria-label="Close modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="receipt-section-title" style="margin-top: 0;">Attendee Information</div>
            <div class="receipt-grid" style="margin-bottom: 20px;">
                <div class="receipt-item">
                    <span class="receipt-label">Full Name</span>
                    <span class="receipt-value" id="modalName">-</span>
                </div>
                <div class="receipt-item">
                    <span class="receipt-label">Email Address</span>
                    <span class="receipt-value" id="modalEmail" style="word-break: break-all;">-</span>
                </div>
                <div class="receipt-item" style="grid-column: 1 / -1;">
                    <span class="receipt-label">Registered On</span>
                    <span class="receipt-value" id="modalDateRegistered">-</span>
                </div>
            </div>

            <div class="receipt-section-title">Event Information</div>
            <div class="receipt-grid" style="margin-bottom: 0;">
                <div class="receipt-item" style="grid-column: 1 / -1;">
                    <span class="receipt-label">Event Name</span>
                    <span class="receipt-value" id="modalEventTitle" style="color: var(--primary);">-</span>
                </div>
                <div class="receipt-item">
                    <span class="receipt-label">Event Date &amp; Time</span>
                    <span class="receipt-value" id="modalEventDate">-</span>
                </div>
                <div class="receipt-item">
                    <span class="receipt-label">Venue / Location</span>
                    <span class="receipt-value" id="modalEventLocation">-</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close-trigger">Close</button>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
