<?php
/**
 * Registration Page
 * Event Registration System
 */

$pageTitle = 'Event Registration';
$activeNav = 'register';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

init_session();
$pdo = getDBConnection();

$errors = [];
$name = '';
$email = '';
$selectedEventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
$selectedEvent = null;
$allEvents = get_upcoming_events($pdo);

// If an event ID was passed, verify it exists
if ($selectedEventId > 0) {
    $selectedEvent = get_event_by_id($pdo, $selectedEventId);
    if (!$selectedEvent) {
        $selectedEventId = 0; // reset if invalid ID
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF verification
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($submittedToken)) {
        $errors['general'] = 'Invalid form submission (security session expired). Please try again.';
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $eventId = (int)($_POST['event_id'] ?? 0);

    // 1. Validate Name
    if ($name === '') {
        $errors['name'] = 'Full name is required and cannot be empty.';
    } elseif (mb_strlen($name) < 2) {
        $errors['name'] = 'Name must be at least 2 characters long.';
    } elseif (mb_strlen($name) > 100) {
        $errors['name'] = 'Name cannot exceed 100 characters.';
    }

    // 2. Validate Email
    if ($email === '') {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please provide a valid email address (e.g., user@example.com).';
    } elseif (mb_strlen($email) > 150) {
        $errors['email'] = 'Email address is too long.';
    }

    // 3. Validate Event
    if ($eventId <= 0) {
        $errors['event_id'] = 'Please choose an event to register for.';
    } else {
        $eventCheck = get_event_by_id($pdo, $eventId);
        if (!$eventCheck) {
            $errors['event_id'] = 'The selected event does not exist.';
        } else {
            $selectedEvent = $eventCheck;
            $selectedEventId = $eventId;
        }
    }

    // 4. Check for duplicate registration (same email + same event)
    if (empty($errors)) {
        $stmtDup = $pdo->prepare("SELECT id FROM `registrations` WHERE `event_id` = :event_id AND LOWER(`email`) = LOWER(:email) LIMIT 1");
        $stmtDup->execute([
            ':event_id' => $eventId,
            ':email' => $email
        ]);
        if ($stmtDup->fetch()) {
            $errors['general'] = "You are already registered for <strong>" . htmlspecialchars($selectedEvent['title']) . "</strong> with the email <strong>" . htmlspecialchars($email) . "</strong>.";
        }
    }

    // 5. If validation passes, insert into database
    if (empty($errors)) {
        try {
            $stmtInsert = $pdo->prepare("INSERT INTO `registrations` (`event_id`, `name`, `email`, `registration_date`) VALUES (:event_id, :name, :email, NOW())");
            $stmtInsert->execute([
                ':event_id' => $eventId,
                ':name' => $name,
                ':email' => $email
            ]);

            $newRegistrationId = (int)$pdo->lastInsertId();

            // Set session registration confirmation
            $_SESSION['last_registration_id'] = $newRegistrationId;
            $token = generate_registration_token($newRegistrationId, $email);

            // Redirect to success page
            header("Location: " . BASE_URL . "success.php?id=" . $newRegistrationId . "&token=" . urlencode($token));
            exit;

        } catch (PDOException $e) {
            $errors['general'] = 'Database error while saving registration: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="max-width: 680px; margin: 0 auto;">

    <!-- Navigation Breadcrumb -->
    <div style="margin-bottom: 20px;" class="no-print">
        <a href="<?= htmlspecialchars(BASE_URL) ?>index.php" style="display: inline-flex; align-items: center; gap: 6px; color: var(--text-muted); font-size: 14px; font-weight: 500;">
            <span>&larr;</span> Back to All Events
        </a>
    </div>

    <div class="card">
        <div style="margin-bottom: 24px;">
            <span class="category-badge" style="margin-bottom: 8px;">Registration Portal</span>
            <h1 style="font-size: 26px; font-weight: 800; color: var(--secondary); margin: 6px 0 8px;">
                Event Registration
            </h1>
            <p style="color: var(--text-muted); font-size: 14.5px; margin: 0;">
                Complete the form below to secure your seat. You will receive an instant confirmation receipt.
            </p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger">
                <span>⚠️</span>
                <div><?= $errors['general'] ?></div>
            </div>
        <?php endif; ?>

        <!-- Highlight Box for Selected Event -->
        <?php if ($selectedEvent): ?>
            <div style="background: #f8fafc; border: 1.5px solid var(--border-color); border-left: 4px solid var(--primary); border-radius: var(--radius-md); padding: 16px 20px; margin-bottom: 24px;">
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--primary); letter-spacing: 0.5px; margin-bottom: 4px;">
                    Selected Event
                </div>
                <div style="font-size: 17px; font-weight: 700; color: var(--secondary); margin-bottom: 6px;">
                    <?= htmlspecialchars($selectedEvent['title']) ?>
                </div>
                <div style="display: flex; gap: 16px; font-size: 13px; color: var(--text-muted); flex-wrap: wrap;">
                    <span>🕒 <?= format_event_date($selectedEvent['event_date']) ?></span>
                    <span>📍 <?= htmlspecialchars($selectedEvent['location']) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form method="POST" action="register.php<?= $selectedEventId ? '?event_id=' . $selectedEventId : '' ?>" id="registrationForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <!-- Field 1: Name -->
            <div class="form-group">
                <label for="name" class="form-label">
                    Full Name <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                    placeholder="Enter your full name (e.g. John Doe)" 
                    value="<?= htmlspecialchars($name) ?>"
                    required
                    autocomplete="name"
                >
                <div class="invalid-feedback">
                    <?= $errors['name'] ?? 'Please enter your full name.' ?>
                </div>
            </div>

            <!-- Field 2: Email -->
            <div class="form-group">
                <label for="email" class="form-label">
                    Email Address <span class="required">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                    placeholder="name@example.com" 
                    value="<?= htmlspecialchars($email) ?>"
                    required
                    autocomplete="email"
                >
                <div class="invalid-feedback">
                    <?= $errors['email'] ?? 'Please enter a valid email address.' ?>
                </div>
                <div class="form-help">Your registration confirmation and event updates will be sent here.</div>
            </div>

            <!-- Field 3: Event (Auto-filled or Selectable) -->
            <div class="form-group">
                <label for="event_id" class="form-label">
                    Event <span class="required">*</span>
                </label>

                <?php if ($selectedEvent): ?>
                    <!-- Auto-filled readonly view when referred from event card -->
                    <input type="hidden" name="event_id" id="event_id" value="<?= (int)$selectedEvent['id'] ?>">
                    <input 
                        type="text" 
                        class="form-control" 
                        value="<?= htmlspecialchars($selectedEvent['title']) ?> (<?= date('M j, Y', strtotime($selectedEvent['event_date'])) ?>)" 
                        readonly
                    >
                    <div class="form-help" style="display: flex; justify-content: space-between; margin-top: 6px;">
                        <span>✓ Auto-filled from selected event</span>
                        <a href="register.php" style="font-size: 12px; color: var(--primary);">Change Event</a>
                    </div>
                <?php else: ?>
                    <!-- Dropdown selector if user opened registration form directly -->
                    <select 
                        name="event_id" 
                        id="event_id" 
                        class="form-control <?= isset($errors['event_id']) ? 'is-invalid' : '' ?>" 
                        required
                    >
                        <option value="">-- Choose an Upcoming Event --</option>
                        <?php foreach ($allEvents as $evt): ?>
                            <option value="<?= (int)$evt['id'] ?>" <?= ($selectedEventId === (int)$evt['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($evt['title']) ?> &bull; <?= date('M j, Y', strtotime($evt['event_date'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= $errors['event_id'] ?? 'Please select an event to attend.' ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Submit Button -->
            <div style="margin-top: 28px;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 13px; font-size: 16px;">
                    Confirm &amp; Register Now &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
