<?php
/**
 * Database Configuration and Connection Handler
 * Event Registration System
 * Supports Local XAMPP and Cloud Platforms (Render, Railway, Docker)
 */

// Cloud / Environment Variable Detection
$dbUrl = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');
$dbHost = 'localhost';
$dbPort = '3306';
$dbName = 'event_management';
$dbUser = 'root';
$dbPass = '';

if ($dbUrl) {
    // Parse URL format: mysql://user:password@host:port/database
    $urlParts = parse_url($dbUrl);
    if (!empty($urlParts['host'])) {
        $dbHost = $urlParts['host'];
        $dbPort = !empty($urlParts['port']) ? (string)$urlParts['port'] : '3306';
        $dbUser = $urlParts['user'] ?? 'root';
        $dbPass = $urlParts['pass'] ?? '';
        $dbName = ltrim($urlParts['path'] ?? 'event_management', '/');
    }
} else {
    // Fallback to explicit env vars or local XAMPP defaults
    $dbHost = getenv('DB_HOST') ?: 'localhost';
    $dbPort = getenv('DB_PORT') ?: '3306';
    $dbName = getenv('DB_NAME') ?: 'event_management';
    $dbUser = getenv('DB_USER') ?: 'root';
    $dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
}

define('DB_HOST', $dbHost);
define('DB_PORT', $dbPort);
define('DB_NAME', $dbName);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);

// Base URL calculation (supports reverse proxies like Render / Cloudflare)
if (!defined('BASE_URL')) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        
    $protocol = $isHttps ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Determine subdirectory relative to document root
    $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $currentDir = str_replace('\\', '/', dirname(__DIR__));
    $subDir = '';
    
    if ($docRoot && strpos($currentDir, $docRoot) === 0) {
        $subDir = substr($currentDir, strlen($docRoot));
    }
    
    $baseUrl = rtrim($protocol . $host . $subDir, '/') . '/';
    define('BASE_URL', $baseUrl);
}

// Function to establish PDO Database Connection
function getDBConnection() {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Check if database doesn't exist
            if ($e->getCode() === 1049) {
                header("Location: " . BASE_URL . "setup.php");
                exit;
            }
            // For other connection errors, render a friendly troubleshooting page
            renderDBErrorPage($e->getMessage());
            exit;
        }
    }

    return $pdo;
}

// Friendly error page in case MySQL is stopped or unreachable
function renderDBErrorPage($errorMessage) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Connection Required - EventHub</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #0f172a; color: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
            .card { background: #1e293b; border-radius: 16px; padding: 36px; max-width: 600px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4); border: 1px solid #334155; }
            h1 { margin-top: 0; color: #f43f5e; font-size: 24px; display: flex; align-items: center; gap: 10px; }
            p { color: #94a3b8; line-height: 1.6; }
            .code-box { background: #0b1120; border: 1px solid #1e293b; border-radius: 8px; padding: 14px; font-family: monospace; color: #fca5a5; font-size: 13px; word-break: break-all; margin: 15px 0; }
            .steps { background: #334155; border-radius: 10px; padding: 18px; margin-top: 20px; }
            .steps h3 { margin-top: 0; font-size: 16px; color: #38bdf8; }
            .steps ol { margin: 0; padding-left: 20px; color: #e2e8f0; font-size: 14px; line-height: 1.8; }
            .btn { display: inline-block; background: #4f46e5; color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 20px; transition: background 0.2s; }
            .btn:hover { background: #4338ca; }
        </style>
    </head>
    <body>
        <div class="card">
            <h1>⚠️ Database Connection Issue</h1>
            <p>The PHP backend could not connect to MySQL. Here is the technical error:</p>
            <div class="code-box"><?= htmlspecialchars($errorMessage) ?></div>
            <div class="steps">
                <h3>Troubleshooting:</h3>
                <ol>
                    <li>If running locally: Ensure <strong>MySQL</strong> is started in <strong>XAMPP</strong>.</li>
                    <li>If running on <strong>Render</strong>: Verify your environment variables (<code>DB_HOST</code>, <code>DB_USER</code>, <code>DB_PASS</code>, <code>DB_NAME</code>, or <code>DATABASE_URL</code>).</li>
                    <li>Once connected, run the setup wizard below:</li>
                </ol>
            </div>
            <div style="text-align: center;">
                <a href="<?= htmlspecialchars(BASE_URL) ?>setup.php" class="btn">Run 1-Click Database Setup</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
