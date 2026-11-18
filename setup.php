<?php
/**
 * 1-Click Database Setup & Seeder Script
 * Event Registration System
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

init_session();

$stepResults = [];
$errorOccurred = false;

// List of simple, easy-to-understand upcoming events
$simpleEvents = [
    [
        'id' => 1,
        'title' => 'Annual Sports Day 2026',
        'description' => 'Join us for a fun-filled day of athletic events, cricket matches, football tournaments, and indoor games. Medals and certificates for all participants!',
        'event_date' => '2026-12-10 09:00:00',
        'location' => 'College Sports Ground',
        'category' => 'Sports',
        'max_seats' => 150
    ],
    [
        'id' => 2,
        'title' => 'College Music & Cultural Night',
        'description' => 'An entertaining evening featuring live acoustic bands, classical and western dance performances, comedy skits, and delicious food stalls.',
        'event_date' => '2026-12-15 17:30:00',
        'location' => 'Main Campus Auditorium',
        'category' => 'Cultural',
        'max_seats' => 300
    ],
    [
        'id' => 3,
        'title' => 'Web Design & Coding Contest',
        'description' => 'A beginner-friendly 2-hour competition where students build a simple, creative web page using HTML, CSS, and basic JavaScript. Exciting prizes to win!',
        'event_date' => '2026-12-22 10:30:00',
        'location' => 'Computer Lab 2',
        'category' => 'Coding',
        'max_seats' => 50
    ],
    [
        'id' => 4,
        'title' => 'Smartphone Photography Workshop',
        'description' => 'Learn essential techniques for taking sharp, professional photos and editing short video reels directly from your mobile phone. No experience needed!',
        'event_date' => '2027-01-08 14:00:00',
        'location' => 'Seminar Hall B',
        'category' => 'Workshop',
        'max_seats' => 60
    ],
    [
        'id' => 5,
        'title' => 'Blood Donation & Free Health Camp',
        'description' => 'A community welfare drive offering free general health checkups, blood pressure screenings, and voluntary blood donation in association with the Red Cross.',
        'event_date' => '2027-01-15 09:30:00',
        'location' => 'Student Activity Center',
        'category' => 'Community',
        'max_seats' => 200
    ]
];

// Action: Reload Simple Events
if (isset($_GET['action']) && $_GET['action'] === 'reload_simple_events') {
    try {
        $pdo = getDBConnection();
        // Upsert simple events
        $stmtUpsert = $pdo->prepare("INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `location`, `category`, `max_seats`) 
            VALUES (:id, :title, :description, :event_date, :location, :category, :max_seats)
            ON DUPLICATE KEY UPDATE 
                `title` = VALUES(`title`),
                `description` = VALUES(`description`),
                `event_date` = VALUES(`event_date`),
                `location` = VALUES(`location`),
                `category` = VALUES(`category`),
                `max_seats` = VALUES(`max_seats`)");

        foreach ($simpleEvents as $evt) {
            $stmtUpsert->execute($evt);
        }

        set_flash('success', 'Simple, easy-to-understand sample events have been loaded successfully!');
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $stepResults[] = ['status' => 'danger', 'msg' => 'Error reloading events: ' . $e->getMessage()];
    }
}

// Process Full Setup Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['auto'])) {
    try {
        // Step 1: Connect to MySQL server without selecting database
        $dsnServer = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
        $pdoServer = new PDO($dsnServer, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $stepResults[] = ['status' => 'success', 'msg' => 'Connected to MySQL server (' . DB_HOST . ':' . DB_PORT . ')'];

        // Step 2: Create Database if not exists
        $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $stepResults[] = ['status' => 'success', 'msg' => 'Database `' . DB_NAME . '` created or verified successfully.'];

        // Step 3: Connect to the specific database
        $pdo = getDBConnection();

        // Step 4: Create `events` table
        $sqlEvents = "CREATE TABLE IF NOT EXISTS `events` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(150) NOT NULL,
            `description` TEXT NOT NULL,
            `event_date` DATETIME NOT NULL,
            `location` VARCHAR(150) NOT NULL,
            `category` VARCHAR(50) NOT NULL DEFAULT 'General',
            `max_seats` INT NOT NULL DEFAULT 100,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $pdo->exec($sqlEvents);
        $stepResults[] = ['status' => 'success', 'msg' => 'Table `events` created or verified.'];

        // Step 5: Create `registrations` table
        $sqlRegistrations = "CREATE TABLE IF NOT EXISTS `registrations` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `event_id` INT NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(150) NOT NULL,
            `registration_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT `fk_registrations_event` FOREIGN KEY (`event_id`) 
                REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $pdo->exec($sqlRegistrations);
        $stepResults[] = ['status' => 'success', 'msg' => 'Table `registrations` created or verified.'];

        // Step 6: Create `admins` table
        $sqlAdmins = "CREATE TABLE IF NOT EXISTS `admins` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password_hash` VARCHAR(255) NOT NULL,
            `full_name` VARCHAR(100) NOT NULL DEFAULT 'Administrator',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $pdo->exec($sqlAdmins);
        $stepResults[] = ['status' => 'success', 'msg' => 'Table `admins` created or verified.'];

        // Step 7: Seed simple events (update existing or insert new)
        $stmtInsertEvent = $pdo->prepare("INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `location`, `category`, `max_seats`) 
            VALUES (:id, :title, :description, :event_date, :location, :category, :max_seats)
            ON DUPLICATE KEY UPDATE 
                `title` = VALUES(`title`),
                `description` = VALUES(`description`),
                `event_date` = VALUES(`event_date`),
                `location` = VALUES(`location`),
                `category` = VALUES(`category`),
                `max_seats` = VALUES(`max_seats`)");

        foreach ($simpleEvents as $evt) {
            $stmtInsertEvent->execute($evt);
        }
        $stepResults[] = ['status' => 'success', 'msg' => 'Loaded 5 simple, easy-to-understand events into `events` table (Sports, Music, Coding, Photography, Health Camp).'];

        // Step 8: Seed default admin if missing
        $stmtAdmin = $pdo->prepare("SELECT COUNT(*) FROM `admins` WHERE `username` = 'admin'");
        $stmtAdmin->execute();
        $adminExists = (bool)$stmtAdmin->fetchColumn();

        if (!$adminExists) {
            $defaultPassword = 'admin123';
            $passwordHash = password_hash($defaultPassword, PASSWORD_DEFAULT);
            $stmtInsertAdmin = $pdo->prepare("INSERT INTO `admins` (`username`, `password_hash`, `full_name`) VALUES ('admin', :hash, 'System Administrator')");
            $stmtInsertAdmin->execute([':hash' => $passwordHash]);
            $stepResults[] = ['status' => 'success', 'msg' => 'Default admin account created: username: <strong>admin</strong> | password: <strong>admin123</strong>'];
        } else {
            $stepResults[] = ['status' => 'info', 'msg' => 'Admin user `admin` already exists in database.'];
        }

        // Step 9: Seed sample registrations if empty
        $stmtRegCount = $pdo->query("SELECT COUNT(*) FROM `registrations`");
        $regCount = (int)$stmtRegCount->fetchColumn();
        if ($regCount === 0) {
            $stmtReg = $pdo->prepare("INSERT INTO `registrations` (`event_id`, `name`, `email`, `registration_date`) VALUES 
                (1, 'Rahul Sharma', 'rahul.sharma@example.com', NOW() - INTERVAL 2 DAY), 
                (2, 'Ananya Patel', 'ananya.patel@example.com', NOW() - INTERVAL 4 HOUR)");
            $stmtReg->execute();
            $stepResults[] = ['status' => 'success', 'msg' => 'Seeded 2 sample attendee registrations for demonstration.'];
        }

    } catch (Exception $e) {
        $errorOccurred = true;
        $stepResults[] = ['status' => 'danger', 'msg' => 'Error: ' . $e->getMessage()];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub Database Setup</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .setup-container { max-width: 680px; margin: 50px auto; padding: 20px; }
        .step-list { list-style: none; padding: 0; margin: 24px 0; }
        .step-item { display: flex; align-items: flex-start; gap: 12px; padding: 12px 16px; border-radius: 8px; margin-bottom: 10px; font-size: 14px; }
        .step-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .step-info { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }
        .step-danger { background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; }
        .creds-box { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 16px; margin: 20px 0; }
        .event-preview-list { list-style: none; padding: 0; margin: 12px 0; }
        .event-preview-item { padding: 8px 12px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 6px; font-size: 13.5px; }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="card">
            <div style="text-align: center; margin-bottom: 24px;">
                <span style="font-size: 48px;">⚙️</span>
                <h1 style="margin: 8px 0; font-size: 26px; color: #1e293b;">EventHub Setup & Database Migration</h1>
                <p style="color: #64748b; margin: 0;">Automated database initialization for MySQL / XAMPP</p>
            </div>

            <?php if (!empty($stepResults)): ?>
                <h3 style="font-size: 16px; color: #334155; margin-bottom: 12px;">Setup Progress:</h3>
                <ul class="step-list">
                    <?php foreach ($stepResults as $step): ?>
                        <li class="step-item step-<?= $step['status'] ?>">
                            <span><?= $step['status'] === 'success' ? '✅' : ($step['status'] === 'info' ? 'ℹ️' : '❌') ?></span>
                            <div><?= $step['msg'] ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if (!$errorOccurred): ?>
                    <div class="alert alert-success">
                        <strong>🎉 Setup Completed Successfully!</strong> Database and simple events are ready.
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap;">
                        <a href="index.php" class="btn btn-primary" style="flex: 1; text-align: center;">🌐 View Homepage & Events</a>
                        <a href="admin/login.php" class="btn btn-secondary" style="flex: 1; text-align: center;">🛡️ Go to Admin Portal</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <strong>Setup encountered an error.</strong> Please ensure MySQL is started in XAMPP and try again.
                    </div>
                    <form method="POST" style="margin-top: 16px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Retry Setup</button>
                    </form>
                <?php endif; ?>

            <?php else: ?>
                <p style="color: #475569; line-height: 1.6;">
                    This setup utility configures your MySQL database (<code><?= htmlspecialchars(DB_NAME) ?></code>), 
                    creates the required tables (<code>events</code>, <code>registrations</code>, <code>admins</code>), and loads 
                    simple, easy-to-understand upcoming events:
                </p>

                <div class="creds-box">
                    <strong style="color: #334155; display: block; margin-bottom: 8px;">✨ Simple Events Included:</strong>
                    <ul class="event-preview-list">
                        <li class="event-preview-item">🏆 <strong>Annual Sports Day 2026</strong> &bull; Cricket, Football & Races</li>
                        <li class="event-preview-item">🎵 <strong>College Music & Cultural Night</strong> &bull; Live Bands & Dance</li>
                        <li class="event-preview-item">💻 <strong>Web Design & Coding Contest</strong> &bull; 2-Hour Coding Challenge</li>
                        <li class="event-preview-item">📸 <strong>Smartphone Photography Workshop</strong> &bull; Mobile Photo & Reels</li>
                        <li class="event-preview-item">❤️ <strong>Blood Donation & Free Health Camp</strong> &bull; Health Checkup Drive</li>
                    </ul>
                </div>

                <form method="POST" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 16px;">
                        🚀 Initialize Database & Load Events
                    </button>
                </form>

                <div style="text-align: center; margin-top: 16px;">
                    <a href="setup.php?action=reload_simple_events" style="font-size: 13.5px; color: var(--primary); font-weight: 600;">
                        🔄 Already set up? Click here to update events to Simple Events
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
