<?php
$actor = $actor ?? 'guest';
$extraScripts = $extraScripts ?? '';
?>

<?php if (in_array($actor, ['student', 'teacher', 'admin'])): ?>
    </main> <!-- End main-content -->
    </div> <!-- End dashboard-layout -->
<?php elseif ($actor === 'auth'): ?>
    </div> <!-- End auth-wrapper -->
<?php else: ?>
    <!-- Footer -->
    <footer style="border-top: 1px solid var(--border-color); padding: 4rem 0; margin-top: 4rem;">
        <div class="container text-center text-muted">
            <p>&copy; 2026 AI Study Hub LMS. Lê Diên Hiếu.</p>
        </div>
    </footer>
<?php endif; ?>

<!-- Scripts -->
<script src="/assets/js/api.js"></script>
<script src="/assets/js/app.js"></script>
<?= $extraScripts ?>
</body>

</html>