<div>
    <h1 class="text-3xl font-bold mb-4">Appointments</h1>
    <p>Manage appointment requests.</p>
    <div class="overflow-x-auto mt-6">
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
                if (empty($appointments)) {
                    echo '<tr><td colspan="7" class="px-4 py-2 border-b text-center text-gray-500">No appointments found.</td></tr>';
                } else {
                    foreach ($appointments as $appt) {
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
