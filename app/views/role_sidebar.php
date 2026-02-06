<?php
$role   = strtoupper($role ?? '');
$active = $active ?? '';
?>

<aside class="fixed left-0 top-0 bottom-0 w-[340px] p-10 bg-gradient-to-b from-[#9e1515] to-[#7b0b0b] text-white flex flex-col font-sans shadow-2xl">

    <!-- BRAND / HEADER -->
    <div class="flex items-center gap-5 pb-8 border-b border-white/20">
        <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center shadow-md">
            <img src="/IDSystem/public/assets/images/BEC-logo.png"
                 alt="BEC Logo"
                 class="w-16 h-16 object-contain" />
        </div>

        <div>
            <h3 class="text-2xl font-extrabold leading-tight">BEC Portal</h3>
            <p class="text-lg uppercase tracking-wide text-white/80">
                <?php echo htmlspecialchars(ucfirst(strtolower($role)), ENT_QUOTES, 'UTF-8'); ?>
            </p>
        </div>
    </div>

    <!-- NAVIGATION -->
    <nav class="flex-1 mt-8">
        <ul class="space-y-3 text-[17px]">

            <?php if (in_array($role, ['STUDENT', 'FACULTY'], true)): ?>

                <li>
                    <a href="/IDSystem" data-page="book"
                       class="flex items-center gap-5 px-5 py-4 rounded-xl transition
                       <?php echo $active === 'book'
                           ? 'bg-white/20 shadow-lg'
                           : 'hover:bg-white/10'; ?>">
                        <span class="w-10 text-center text-xl">
                            <i class="fa-solid fa-calendar-days"></i>
                        </span>
                        <span class="font-semibold">Book Appointment</span>
                    </a>
                </li>

                <li>
                    <a href="/IDSystem" data-page="schedules"
                       class="flex items-center gap-5 px-5 py-4 rounded-xl transition
                       <?php echo $active === 'schedules'
                           ? 'bg-white/20 shadow-lg'
                           : 'hover:bg-white/10'; ?>">
                        <span class="w-10 text-center text-xl">
                            <i class="fa-solid fa-calendar-check"></i>
                        </span>
                        <span class="font-semibold">My Schedule</span>
                    </a>
                </li>

                <li>
                    <a href="/IDSystem" data-page="profile"
                       class="flex items-center gap-5 px-5 py-4 rounded-xl transition
                       <?php echo $active === 'profile'
                           ? 'bg-white/20 shadow-lg'
                           : 'hover:bg-white/10'; ?>">
                        <span class="w-10 text-center text-xl">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span class="font-semibold">Profile</span>
                    </a>
                </li>

            <?php elseif (in_array($role, ['ADMIN', 'SUPERADMIN'], true)): ?>

                <li>
                    <a href="/IDSystem" data-page="dashboard"
                       class="flex items-center gap-5 px-5 py-4 rounded-xl transition
                       <?php echo $active === 'dashboard'
                           ? 'bg-white/20 shadow-lg'
                           : 'hover:bg-white/10'; ?>">
                        <span class="w-10 text-center text-xl">
                            <i class="fa-solid fa-house"></i>
                        </span>
                        <span class="font-semibold">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="/IDSystem" data-page="appointments"
                       class="flex items-center gap-5 px-5 py-4 rounded-xl transition
                       <?php echo $active === 'appointments'
                           ? 'bg-white/20 shadow-lg'
                           : 'hover:bg-white/10'; ?>">
                        <span class="w-10 text-center text-xl">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </span>
                        <span class="font-semibold">Appointments</span>
                    </a>
                </li>

                <li>
                    <a href="/IDSystem" data-page="settings"
                       class="flex items-center gap-5 px-5 py-4 rounded-xl transition
                       <?php echo $active === 'settings'
                           ? 'bg-white/20 shadow-lg'
                           : 'hover:bg-white/10'; ?>">
                        <span class="w-10 text-center text-xl">
                            <i class="fa-solid fa-gear"></i>
                        </span>
                        <span class="font-semibold">Settings</span>
                    </a>
                </li>

                <?php if ($role === 'SUPERADMIN'): ?>
                    <li>
                        <a href="/IDSystem" data-page="manage_users"
                               class="flex items-center gap-5 px-5 py-4 rounded-xl transition
                                   <?php echo $active === 'manage_users'
                                       ? 'bg-white/20 shadow-lg'
                                       : 'hover:bg-white/10'; ?>">
                            <span class="w-10 text-center text-xl">
                                <i class="fa-solid fa-users"></i>
                            </span>
                            <span class="font-semibold">Manage Users</span>
                        </a>
                    </li>
                <?php endif; ?>

            <?php else: ?>

                <li>
                    <a href="/IDSystem"
                       class="flex items-center gap-5 px-5 py-4 rounded-xl hover:bg-white/10">
                        <span class="w-10 text-center text-xl">
                            <i class="fa-solid fa-house"></i>
                        </span>
                        <span class="font-semibold">Home</span>
                    </a>
                </li>

            <?php endif; ?>
        </ul>
    </nav>

    <!-- LOGOUT -->
    <div class="pt-8 border-t border-white/20">
        <form action="/IDSystem/logout" method="POST">
            <button type="submit"
                    class="w-full flex items-center justify-center gap-4 px-5 py-4 rounded-xl
                           bg-white/10 hover:bg-white/20 transition font-semibold text-lg">
                <i class="fa-solid fa-right-from-bracket text-xl"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>
