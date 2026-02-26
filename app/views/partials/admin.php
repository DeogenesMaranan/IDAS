<?php
// Parent partial for admin and superadmin pages. Includes child partials from the admin folder.
?>

<section id="dashboard" class="spa-section <?php echo $active === 'dashboard' ? '' : 'hidden'; ?>">
    <?php Response::partial('partials/admin/dashboard', ['active' => $active ?? null, 'role' => $role ?? null, 'sessionUser' => $sessionUser ?? null]); ?>
</section>

<section id="appointments" class="spa-section <?php echo $active === 'appointments' ? '' : 'hidden'; ?>">
    <?php Response::partial('partials/admin/appointments', ['active' => $active ?? null, 'role' => $role ?? null, 'sessionUser' => $sessionUser ?? null]); ?>
</section>

<section id="settings" class="spa-section <?php echo $active === 'settings' ? '' : 'hidden'; ?>">
    <?php Response::partial('partials/admin/settings', ['active' => $active ?? null, 'role' => $role ?? null, 'sessionUser' => $sessionUser ?? null]); ?>
</section>

<?php if ($role === 'SUPERADMIN'): ?>
    <section id="manage_users" class="spa-section <?php echo $active === 'manage_users' ? '' : 'hidden'; ?>">
        <?php Response::partial('partials/admin/manage_users', ['active' => $active ?? null, 'role' => $role ?? null, 'sessionUser' => $sessionUser ?? null]); ?>
    </section>
<?php endif; ?>
