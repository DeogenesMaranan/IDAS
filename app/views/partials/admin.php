<?php
// Parent partial for admin and superadmin pages. Includes child partials from the admin folder.
?>

<!-- Admin: Dashboard -->
<section id="dashboard" class="spa-section <?php echo $active === 'dashboard' ? '' : 'hidden'; ?>">
    <?php require __DIR__ . '/admin/dashboard.php'; ?>
</section>

<!-- Admin: Appointments -->
<section id="appointments" class="spa-section <?php echo $active === 'appointments' ? '' : 'hidden'; ?>">
    <?php require __DIR__ . '/admin/appointments.php'; ?>
</section>

<!-- Admin: Settings -->
<section id="settings" class="spa-section <?php echo $active === 'settings' ? '' : 'hidden'; ?>">
    <?php require __DIR__ . '/admin/settings.php'; ?>
</section>

<?php if ($role === 'SUPERADMIN'): ?>
    <section id="manage_users" class="spa-section <?php echo $active === 'manage_users' ? '' : 'hidden'; ?>">
        <?php require __DIR__ . '/admin/manage_users.php'; ?>
    </section>
<?php endif; ?>
