<!-- Scripts -->
<script src="../assets/js/main.js"></script>
<script src="../assets/js/admin.js"></script>

<!-- Notifications -->
<?php if (isset($_SESSION['success'])): ?>
<script>
    showNotification('<?= $_SESSION['success'] ?>', 'success');
</script>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<script>
    showNotification('<?= $_SESSION['error'] ?>', 'error');
</script>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>

</body>
</html>