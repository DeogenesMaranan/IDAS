<?php
// Parent partial for user pages (handles both student and faculty)
?>

<section id="book" class="spa-section <?php echo $active === 'book' ? '' : 'hidden'; ?>">
    <?php Response::partial('partials/user/booking', ['role' => $role ?? null, 'sessionUser' => $sessionUser ?? null, 'active' => $active ?? null]); ?>
</section>

<section id="schedules" class="spa-section <?php echo $active === 'schedules' ? '' : 'hidden'; ?>">
    <?php Response::partial('partials/user/schedules', ['active' => $active ?? null, 'sessionUser' => $sessionUser ?? null]); ?>
</section>

<section id="profile" class="spa-section <?php echo $active === 'profile' ? '' : 'hidden'; ?>">
    <?php Response::partial('partials/user/profile', ['fullName' => $fullName ?? '', 'email' => $sessionUser['email'] ?? ($email ?? '' )]); ?>
</section>
