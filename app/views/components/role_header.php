<?php
$fullNameRaw = $fullName ?? ($sessionUser['fullName'] ?? '');
$fullName = htmlspecialchars($fullNameRaw, ENT_QUOTES, 'UTF-8');
$firstLetter = mb_strtoupper(mb_substr(trim($fullNameRaw), 0, 1, 'UTF-8'), 'UTF-8');
$profilePhoto = $profilePhoto ?? ($sessionUser['profilePhoto'] ?? null);
?>
<header class="w-full bg-white shadow-md sticky top-0 z-30 py-6">
    <div class="flex items-center justify-end gap-6 px-10">
    <button type="button" class="relative p-2 rounded-full hover:bg-gray-100 transition">
        <i class="fa-solid fa-bell text-2xl text-gray-700"></i>
    </button>
    <div class="relative" id="profile-dropdown-container">
        <button type="button" class="flex items-center gap-3 focus:outline-none" id="profile-dropdown-toggle">
            <?php if ($profilePhoto): ?>
                <img src="<?php echo htmlspecialchars($profilePhoto, ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Photo" class="w-10 h-10 rounded-full object-cover border-2 border-gray-300 bg-gray-200" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" />
                <span style="display:none;" class="w-10 h-10 rounded-full bg-[#9e1515] text-white flex items-center justify-center font-bold text-xl border-2 border-gray-300"> <?php echo $firstLetter; ?> </span>
            <?php else: ?>
                <span class="w-10 h-10 rounded-full bg-[#9e1515] text-white flex items-center justify-center font-bold text-xl border-2 border-gray-300"> <?php echo $firstLetter; ?> </span>
            <?php endif; ?>
            <span class="flex flex-col max-w-[180px] truncate items-start">
                <span class="font-semibold text-gray-800 text-lg truncate"> <?php echo $fullName; ?> </span>
                <span class="text-sm text-gray-600 font-semibold truncate -mt-0.5">
                    <?php echo isset($role) ? htmlspecialchars(ucfirst(strtolower($role)), ENT_QUOTES, 'UTF-8') : ''; ?>
                </span>
            </span>
        </button>
        <div id="profile-dropdown-menu" class="absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow-lg opacity-0 pointer-events-none transition-opacity duration-200 z-50">
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var toggle = document.getElementById('profile-dropdown-toggle');
                var menu = document.getElementById('profile-dropdown-menu');
                var container = document.getElementById('profile-dropdown-container');
                if (toggle && menu) {
                    toggle.addEventListener('click', function(e) {
                        e.stopPropagation();
                        var open = menu.classList.contains('opacity-100');
                        if (open) {
                            menu.classList.remove('opacity-100', 'pointer-events-auto');
                            menu.classList.add('opacity-0', 'pointer-events-none');
                        } else {
                            menu.classList.remove('opacity-0', 'pointer-events-none');
                            menu.classList.add('opacity-100', 'pointer-events-auto');
                        }
                    });
                    document.addEventListener('click', function(e) {
                        if (!container.contains(e.target)) {
                            menu.classList.remove('opacity-100', 'pointer-events-auto');
                            menu.classList.add('opacity-0', 'pointer-events-none');
                        }
                    });
                }
            });
            </script>
            <form action="/IDSystem/logout" method="POST">
                <button type="submit" class="w-full text-left px-5 py-3 hover:bg-gray-100 text-gray-700 font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </div>
  </div>
</header>
