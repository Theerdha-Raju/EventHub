<?php
/**
 * Registration Success Confirmation Page
 * Event Registration System
 */

$pageTitle = 'Registration Confirmed';
$activeNav = '';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

init_session();
$pdo = getDBConnection();

// Registration ID can be obtained from GET parameter or Session
$registrationId = isset($_GET['id']) ? (int)$_GET['id'] : ($_SESSION['last_registration_id'] ?? 0);
$token = $_GET['token'] ?? '';

$registration = null;
if ($registrationId > 0) {
    $registration = get_registration_by_id($pdo, $registrationId);
}

// If no registration was found, redirect to home
if (!$registration) {
    set_flash('info', 'No recent registration summary found. Please select an event to register.');
    header("Location: " . BASE_URL . "index.php");
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="success-container">
    <div class="receipt-card">
        <!-- Header Banner with Checkmark -->
        <div class="receipt-header">
            <div class="checkmark-circle">✓</div>
            <h1 class="receipt-title">Registration Confirmed!</h1>
            <p style="margin: 0; opacity: 0.95; font-size: 15px;">
                We look forward to seeing you at the event.
            </p>
        </div>

        <div class="receipt-body">
            <!-- Ticket Reference Box -->
            <div class="ticket-box">
                <div>
                    <div style="font-size: 12px; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 0.5px;">
                        Registration Reference
                    </div>
                    <div class="ticket-number">
                        #EVT-<?= str_pad((string)$registration['id'], 6, '0', STR_PAD_LEFT) ?>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 12px; color: var(--text-muted);">Status</div>
                    <span class="table-badge" style="background: #dcfce7; color: #166534; font-weight: 700;">
                        CONFIRMED
                    </span>
                </div>
            </div>

            <!-- Attendee Details Section -->
            <div class="receipt-section-title">Attendee Information</div>
            <div class="receipt-grid">
                <div class="receipt-item">
                    <span class="receipt-label">Full Name</span>
                    <span class="receipt-value"><?= htmlspecialchars($registration['name']) ?></span>
                </div>
                <div class="receipt-item">
                    <span class="receipt-label">Email Address</span>
                    <span class="receipt-value"><?= htmlspecialchars($registration['email']) ?></span>
                </div>
                <div class="receipt-item">
                    <span class="receipt-label">Registration Date</span>
                    <span class="receipt-value"><?= format_short_date($registration['registration_date']) ?></span>
                </div>
            </div>

            <!-- Event Details Section -->
            <div class="receipt-section-title">Event Details</div>
            <div class="receipt-grid">
                <div class="receipt-item" style="grid-column: 1 / -1;">
                    <span class="receipt-label">Event Name</span>
                    <span class="receipt-value" style="font-size: 17px; color: var(--primary);">
                        <?= htmlspecialchars($registration['event_title']) ?>
                    </span>
                </div>
                <div class="receipt-item">
                    <span class="receipt-label">Date &amp; Time</span>
                    <span class="receipt-value"><?= format_event_date($registration['event_date']) ?></span>
                </div>
                <div class="receipt-item">
                    <span class="receipt-label">Location / Venue</span>
                    <span class="receipt-value"><?= htmlspecialchars($registration['event_location']) ?></span>
                </div>
                <div class="receipt-item">
                    <span class="receipt-label">Category</span>
                    <span class="receipt-value"><?= htmlspecialchars($registration['event_category']) ?></span>
                </div>
            </div>

            <?php if (!empty($registration['event_description'])): ?>
                <div style="background: #f8fafc; border-radius: var(--radius-sm); padding: 14px; font-size: 13.5px; color: #475569; margin-bottom: 24px;">
                    <strong style="color: var(--secondary); display: block; margin-bottom: 4px;">About This Event:</strong>
                    <?= nl2br(htmlspecialchars($registration['event_description'])) ?>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 12px; flex-wrap: wrap;" class="no-print">
                <button type="button" onclick="window.print()" class="btn btn-secondary" style="flex: 1;">
                    🖨️ Print / Save Confirmation
                </button>
                <a href="<?= htmlspecialchars(BASE_URL) ?>index.php" class="btn btn-primary" style="flex: 1;">
                    🌐 Browse More Events &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
