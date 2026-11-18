        </div><!-- /.container -->
    </main><!-- /.main-content -->

    <footer class="site-footer no-print">
        <div class="container">
            <p>&copy; <?= date('Y') ?> EventHub &bull; Modern Event Registration & Management System.</p>
        </div>
    </footer>

    <!-- Custom JavaScript -->
    <script src="<?= htmlspecialchars(BASE_URL) ?>assets/js/main.js"></script>
    <?php if (isset($extraScripts)): ?>
        <?= $extraScripts ?>
    <?php endif; ?>
</body>
</html>
