<?php
/**
 * Admin Login Page
 * Event Registration System
 */

$pageTitle = 'Admin Login';
$activeNav = 'login';

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

init_session();

// Prevent browser from caching the login page
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

// If already logged in, redirect straight to admin dashboard
if (is_admin_logged_in()) {
    header("Location: " . BASE_URL . "admin/index.php");
    exit;
}

$pdo = getDBConnection();
$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($submittedToken)) {
        $error = 'Security session expired. Please refresh and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Please enter both username and password.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM `admins` WHERE `username` = :username LIMIT 1");
                $stmt->execute([':username' => $username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password_hash'])) {
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);

                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_name'] = $admin['full_name'] ?? 'Administrator';

                    set_flash('success', 'Welcome back, ' . htmlspecialchars($admin['username']) . '!');
                    header("Location: " . BASE_URL . "admin/index.php");
                    exit;
                } else {
                    $error = 'Invalid username or password. Please try again.';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 440px; margin: 40px auto 60px;">
    <div class="card">
        <div style="text-align: center; margin-bottom: 28px;">
            <div style="width: 56px; height: 56px; background: var(--primary-light); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 14px;">
                🔒
            </div>
            <h1 style="font-size: 22px; font-weight: 800; color: var(--secondary); margin-bottom: 6px;">
                Admin Portal Login
            </h1>
            <p style="color: var(--text-muted); font-size: 13.5px; margin: 0;">
                Sign in to manage events and review registrations.
            </p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <span>⚠️</span>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>


        <form method="POST" action="login.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-control" 
                    value="<?= htmlspecialchars($username) ?>" 
                    placeholder="Enter admin username"
                    required 
                    autofocus
                    autocomplete="username"
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="Enter admin password"
                    required 
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">
                Sign In to Dashboard &rarr;
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 13.5px;">
            <a href="<?= htmlspecialchars(BASE_URL) ?>index.php" style="color: var(--text-muted);">
                &larr; Return to Public Site
            </a>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
