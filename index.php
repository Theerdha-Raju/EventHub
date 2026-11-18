<?php
/**
 * Homepage - Upcoming Events List
 * Event Registration System
 */

$pageTitle = 'Upcoming Events';
$activeNav = 'events';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDBConnection();
$events = [];
$errorMsg = null;

try {
    $events = get_upcoming_events($pdo);
} catch (Exception $e) {
    $errorMsg = $e->getMessage();
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner -->
<section class="hero no-print">
    <div class="hero-content">
        <span class="hero-tag">✨ Discover & Learn</span>
        <h1 class="hero-title">Explore Upcoming Tech, AI & Design Events</h1>
        <p class="hero-subtitle">
            Expand your skills, network with innovators, and secure your spot in industry-leading workshops, summits, and conferences.
        </p>
    </div>
</section>

<!-- Section Controls: Title & Live Search -->
<div class="section-header">
    <div class="section-title">
        <span>📅</span>
        <span>Featured Upcoming Events (<?= count($events) ?>)</span>
    </div>
    <div class="search-box-wrapper no-print">
        <span class="search-icon">🔍</span>
        <input 
            type="text" 
            id="eventSearch" 
            class="search-input" 
            placeholder="Search events by title, topic, or venue..." 
            autocomplete="off"
        >
    </div>
</div>

<?php if ($errorMsg): ?>
    <div class="alert alert-danger">
        <strong>Error loading events:</strong> <?= htmlspecialchars($errorMsg) ?>
    </div>
<?php endif; ?>

<!-- Events Grid -->
<?php if (!empty($events)): ?>
    <div class="events-grid" id="eventsGrid">
        <?php foreach ($events as $event): ?>
            <article 
                class="event-card" 
                data-title="<?= htmlspecialchars($event['title']) ?>" 
                data-category="<?= htmlspecialchars($event['category'] ?? 'General') ?>" 
                data-location="<?= htmlspecialchars($event['location'] ?? '') ?>"
            >
                <div class="event-card-header">
                    <span class="category-badge">
                        <?= htmlspecialchars($event['category'] ?? 'Event') ?>
                    </span>
                    <span class="seats-info">
                        🎟️ <?= htmlspecialchars($event['max_seats'] ?? 100) ?> Seats
                    </span>
                </div>

                <h2 class="event-title">
                    <?= htmlspecialchars($event['title']) ?>
                </h2>

                <div class="event-meta">
                    <div class="meta-item">
                        <span class="meta-icon">🕒</span>
                        <strong><?= format_event_date($event['event_date']) ?></strong>
                    </div>
                    <div class="meta-item">
                        <span class="meta-icon">📍</span>
                        <span><?= htmlspecialchars($event['location'] ?? 'Virtual / Online') ?></span>
                    </div>
                </div>

                <p class="event-desc">
                    <?= nl2br(htmlspecialchars($event['description'])) ?>
                </p>

                <div class="event-card-footer">
                    <a href="<?= htmlspecialchars(BASE_URL) ?>register.php?event_id=<?= (int)$event['id'] ?>" class="btn btn-primary" style="width: 100%;">
                        <span>Register Now</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <!-- Hidden message when live search yields no match -->
    <div id="searchEmptyNotice" class="empty-state" style="display: none; margin-top: 20px;">
        <div class="empty-icon">🔍</div>
        <h3 class="empty-title">No matching events found</h3>
        <p class="empty-desc">Try searching with different keywords or clear the search input to see all events.</p>
    </div>

<?php else: ?>
    <!-- Empty Database Notice -->
    <div class="empty-state">
        <div class="empty-icon">🎪</div>
        <h3 class="empty-title">No Events Available Yet</h3>
        <p class="empty-desc">There are no upcoming events in the database right now. You can initialize the database and load sample events automatically.</p>
        <a href="<?= htmlspecialchars(BASE_URL) ?>setup.php" class="btn btn-primary">
            🚀 Run 1-Click Database Setup &amp; Seeder
        </a>
    </div>
<?php endif; ?>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
