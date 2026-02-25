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
                        echo '<button class="bg-blue-500 text-white px-2 py-1 rounded mr-1 view-btn" data-id="' . htmlspecialchars($appt["id"]) . '">View</button>';
                        if ($appt["status"] === "APPROVED") {
                            echo '<button class="bg-gray-400 text-white px-2 py-1 rounded mr-1 approve-btn" data-id="' . htmlspecialchars($appt["id"]) . '" disabled style="opacity:0.6;cursor:not-allowed;">Approve</button>';
                            echo '<button class="bg-gray-400 text-white px-2 py-1 rounded mr-1 resched-btn" data-id="' . htmlspecialchars($appt["id"]) . '" disabled style="opacity:0.6;cursor:not-allowed;">Resched</button>';
                            echo '<button class="bg-gray-400 text-white px-2 py-1 rounded cancel-btn" data-id="' . htmlspecialchars($appt["id"]) . '" disabled style="opacity:0.6;cursor:not-allowed;">Cancel</button>';
                        } else {
                            echo '<button class="bg-green-500 text-white px-2 py-1 rounded mr-1 approve-btn" data-id="' . htmlspecialchars($appt["id"]) . '">Approve</button>';
                            echo '<button class="bg-yellow-500 text-white px-2 py-1 rounded mr-1 resched-btn" data-id="' . htmlspecialchars($appt["id"]) . '">Resched</button>';
                            echo '<button class="bg-red-500 text-white px-2 py-1 rounded cancel-btn" data-id="' . htmlspecialchars($appt["id"]) . '">Cancel</button>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- Reschedule Modal -->
    <div id="resched-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded shadow-lg p-6 w-80 relative">
            <button id="close-resched-modal" class="absolute top-2 right-2 text-gray-500 hover:text-black">&times;</button>
            <h2 class="text-xl font-bold mb-4">Reschedule Appointment</h2>
            <form id="resched-form">
                <input type="hidden" name="id" id="resched-appt-id">
                <label class="block mb-2">Date</label>
                <input type="date" name="date" id="resched-date" class="border rounded px-2 py-1 w-full mb-3" required>
                <label class="block mb-2">Time</label>
                <input type="time" name="time" id="resched-time" class="border rounded px-2 py-1 w-full mb-4" required>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">Submit</button>
            </form>
        </div>
    </div>

</div>
<script>
// Helper: AJAX POST
function ajaxPost(url, data, cb) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() { cb(xhr.responseText, xhr.status); };
    xhr.send(new URLSearchParams(data).toString());
}

// Reschedule
const reschedModal = document.getElementById('resched-modal');
const closeReschedModal = document.getElementById('close-resched-modal');
const reschedForm = document.getElementById('resched-form');
let reschedIdInput = document.getElementById('resched-appt-id');

document.querySelectorAll('.resched-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        reschedIdInput.value = this.dataset.id;
        reschedModal.classList.remove('hidden');
    });
});
closeReschedModal.addEventListener('click', () => {
    reschedModal.classList.add('hidden');
});
reschedForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const id = reschedIdInput.value;
    const date = document.getElementById('resched-date').value;
    const time = document.getElementById('resched-time').value;
    ajaxPost('/IDSystem/admin/appointments/reschedule', {id, date, time}, (resp, status) => {
        if (status === 200) location.reload();
        else alert('Failed to reschedule.');
    });
});
// Approve
document.querySelectorAll('.approve-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Approve this appointment?')) return;
        ajaxPost('/IDSystem/admin/appointments/approve', {id: this.dataset.id}, (resp, status) => {
            if (status === 200) location.reload();
            else alert('Failed to approve.');
        });
    });
});

// Cancel
document.querySelectorAll('.cancel-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Cancel this appointment?')) return;
        ajaxPost('/IDSystem/admin/appointments/cancel', {id: this.dataset.id}, (resp, status) => {
            if (status === 200) location.reload();
            else alert('Failed to cancel.');
        });
    });
});

// View
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        ajaxPost('/IDSystem/admin/appointments/view', {id: this.dataset.id}, (resp, status) => {
            if (status === 200) {
                try {
                    const data = JSON.parse(resp);
                    let details = 'Appointment Details:\n';
                    for (const key in data) {
                        if (data.hasOwnProperty(key)) {
                            details += key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) + ': ' + (data[key] ?? '') + '\n';
                        }
                    }
                    alert(details);
                } catch (e) {
                    alert(resp);
                }
            } else {
                alert('Failed to fetch details.');
            }
        });
    });
});
</script>
