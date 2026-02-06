<?php
// Parent partial for user pages (handles both student and faculty)
?>

<section id="book" class="spa-section <?php echo $active === 'book' ? '' : 'hidden'; ?>">
    <?php require __DIR__ . '/user/booking.php'; ?>
</section>

<section id="schedules" class="spa-section <?php echo $active === 'schedules' ? '' : 'hidden'; ?>">
    <?php require __DIR__ . '/user/schedules.php'; ?>
</section>

<section id="profile" class="spa-section <?php echo $active === 'profile' ? '' : 'hidden'; ?>">
    <?php require __DIR__ . '/user/profile.php'; ?>
</section>
