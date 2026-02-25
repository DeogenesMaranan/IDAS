<div>
    <h1 class="text-3xl font-bold mb-4">Appointments</h1>
    <p>Manage appointment requests.</p>
    <form method="GET" class="flex items-center gap-4 mb-4">
        <input type="text" name="search" placeholder="Search by name or ref number" value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="border rounded px-3 py-2 w-64" />
        <select name="status" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            <option value="PENDING" <?php echo (($_GET['status'] ?? '') === 'PENDING') ? 'selected' : ''; ?>>Pending</option>
            <option value="APPROVED" <?php echo (($_GET['status'] ?? '') === 'APPROVED') ? 'selected' : ''; ?>>Approved</option>
            <option value="RESCHEDULED" <?php echo (($_GET['status'] ?? '') === 'RESCHEDULED') ? 'selected' : ''; ?>>Rescheduled</option>
            <option value="CANCELED" <?php echo (($_GET['status'] ?? '') === 'CANCELED') ? 'selected' : ''; ?>>Canceled</option>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
    </form>
    <div class="overflow-x-auto mt-2">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b">Ref Number</th>
                    <th class="px-4 py-2 border-b">Name</th>
                    <th class="px-4 py-2 border-b">Department</th>
                    <th class="px-4 py-2 border-b">Date & Time</th>
                    <th class="px-4 py-2 border-b">ID Type</th>
                    <th class="px-4 py-2 border-b">Status</th>
                    <th class="px-4 py-2 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once __DIR__ . '/../../../models/Appointment.php';
                $appointments = Appointment::getAllWithProfile();
                // Filter by search and status
                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                $status = isset($_GET['status']) ? trim($_GET['status']) : '';
                $filtered = [];
                foreach ($appointments as $appt) {
                    $match = true;
                    if ($search !== '') {
                        $match = stripos($appt['full_name'], $search) !== false || stripos($appt['id'], $search) !== false;
                    }
                    if ($match && $status !== '') {
                        $match = $appt['status'] === $status;
                    }
                    if ($match) $filtered[] = $appt;
                }
                if (empty($filtered)) {
                    echo '<tr><td colspan="7" class="px-4 py-2 border-b text-center text-gray-500">No appointments found.</td></tr>';
                } else {
                    foreach ($filtered as $appt) {
                        echo '<tr>';
                        echo '<td class="px-4 py-2 border-b">' . htmlspecialchars($appt["id"]) . '</td>';
                        echo '<td class="px-4 py-2 border-b">' . htmlspecialchars($appt["full_name"]) . '</td>';
                        echo '<td class="px-4 py-2 border-b">' . htmlspecialchars($appt["department"]) . '</td>';
                        echo '<td class="px-4 py-2 border-b">' . htmlspecialchars(date("M d, Y H:i", strtotime($appt["scheduled_at"]))) . '</td>';
                        echo '<td class="px-4 py-2 border-b">' . htmlspecialchars($appt["id_type"]) . '</td>';
                        echo '<td class="px-4 py-2 border-b">' . htmlspecialchars($appt["status"]) . '</td>';
                        echo '<td class="px-4 py-2 border-b">';
                        echo '<button class="bg-blue-500 text-white px-2 py-1 rounded mr-1">View</button>';
                        echo '<button class="bg-green-500 text-white px-2 py-1 rounded mr-1">Approve</button>';
                        echo '<button class="bg-yellow-500 text-white px-2 py-1 rounded mr-1">Resched</button>';
                        echo '<button class="bg-red-500 text-white px-2 py-1 rounded">Cancel</button>';
                        echo '</td>';
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
