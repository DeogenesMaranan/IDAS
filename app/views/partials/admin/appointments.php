<div>
    <h1 class="text-3xl font-bold mb-4">Appointments</h1>
    <p>Manage appointment requests.</p>
    <!-- Summary Bar -->
    <div id="summary-bar" class="flex gap-4 mb-4">
        <div class="bg-gray-100 rounded px-4 py-2 flex flex-col items-center">
            <span class="text-lg font-semibold" id="summary-total">0</span>
            <span class="text-xs text-gray-600">Total</span>
        </div>
        <div class="bg-yellow-100 rounded px-4 py-2 flex flex-col items-center">
            <span class="text-lg font-semibold" id="summary-pending">0</span>
            <span class="text-xs text-gray-600">Pending</span>
        </div>
        <div class="bg-green-100 rounded px-4 py-2 flex flex-col items-center">
            <span class="text-lg font-semibold" id="summary-approved">0</span>
            <span class="text-xs text-gray-600">Approved</span>
        </div>
        <div class="bg-blue-100 rounded px-4 py-2 flex flex-col items-center">
            <span class="text-lg font-semibold" id="summary-completed">0</span>
            <span class="text-xs text-gray-600">Completed</span>
        </div>
    </div>
    <form id="search-form" class="flex items-center gap-4 mb-4" autocomplete="off">
        <input type="text" name="search" id="search-input" placeholder="Search by name or ref number" value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="border rounded px-3 py-2 w-64" />
        <select name="status" id="status-select" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            <option value="PENDING" <?php echo (($_GET['status'] ?? '') === 'PENDING') ? 'selected' : ''; ?>>Pending</option>
            <option value="APPROVED" <?php echo (($_GET['status'] ?? '') === 'APPROVED') ? 'selected' : ''; ?>>Approved</option>
            <option value="RESCHEDULED" <?php echo (($_GET['status'] ?? '') === 'RESCHEDULED') ? 'selected' : ''; ?>>Rescheduled</option>
            <option value="CANCELED" <?php echo (($_GET['status'] ?? '') === 'CANCELED') ? 'selected' : ''; ?>>Canceled</option>
        </select>
        <button type="button" id="export-excel-btn" class="bg-green-600 text-white px-4 py-2 rounded">Export</button>
    </form>
    <div class="overflow-x-auto mt-2">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b">Ref Number</th>
                    <th class="px-4 py-2 border-b">Name</th>
                    <th class="px-4 py-2 border-b">Department</th>
                    <th class="px-4 py-2 border-b">Date & Time</th>
                    <th class="px-4 py-2 border-b">ID Number</th>
                    <th class="px-4 py-2 border-b">Status</th>
                    <th class="px-4 py-2 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
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
// Export to Excel
document.getElementById('export-excel-btn').addEventListener('click', function() {
    const search = searchInput.value;
    const status = statusSelect.value;
    // Build query string
    const params = new URLSearchParams({search, status});
    // Create a hidden iframe to trigger download
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = '/IDSystem/admin/appointments/export-excel?' + params.toString();
    document.body.appendChild(iframe);
    // Remove iframe after download starts
    setTimeout(() => document.body.removeChild(iframe), 2000);
});
// Helper: AJAX POST
function ajaxPost(url, data, cb) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() { cb(xhr.responseText, xhr.status); };
    xhr.send(new URLSearchParams(data).toString());
}

// Helper: AJAX GET
function ajaxGet(url, cb) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', url);
    xhr.onload = function() { cb(xhr.responseText, xhr.status); };
    xhr.send();
}

// Render appointments table
function renderAppointmentsTable(appointments) {
    const tbody = document.querySelector('table tbody');
    tbody.innerHTML = '';
    if (!appointments || appointments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-2 border-b text-center text-gray-500">No appointments found.</td></tr>';
        return;
    }
    appointments.forEach(appt => {
        let row = '<tr>';
        row += '<td class="px-4 py-2 border-b">' + escapeHtml(appt.id) + '</td>';
        row += '<td class="px-4 py-2 border-b">' + escapeHtml(appt.full_name) + '</td>';
        row += '<td class="px-4 py-2 border-b">' + escapeHtml(appt.department) + '</td>';
        row += '<td class="px-4 py-2 border-b">' + escapeHtml(formatDate(appt.scheduled_at)) + '</td>';
        row += '<td class="px-4 py-2 border-b">' + escapeHtml(appt.id_type) + '</td>';
        row += '<td class="px-4 py-2 border-b">' + escapeHtml(appt.status) + '</td>';
        row += '<td class="px-4 py-2 border-b">';
        if (appt.status === 'CANCELED') {
            row += '<button class="bg-gray-400 text-white px-2 py-1 rounded mr-1 view-btn" data-id="' + escapeHtml(appt.id) + '" disabled style="opacity:0.6;cursor:not-allowed;">View</button>';
        } else {
            row += '<button class="bg-blue-500 text-white px-2 py-1 rounded mr-1 view-btn" data-id="' + escapeHtml(appt.id) + '">View</button>';
        }
        if (appt.status === 'APPROVED') {
            row += '<button class="bg-gray-400 text-white px-2 py-1 rounded mr-1 approve-btn" data-id="' + escapeHtml(appt.id) + '" disabled style="opacity:0.6;cursor:not-allowed;">Approve</button>';
            row += '<button class="bg-gray-400 text-white px-2 py-1 rounded mr-1 resched-btn" data-id="' + escapeHtml(appt.id) + '" disabled style="opacity:0.6;cursor:not-allowed;">Resched</button>';
            row += '<button class="bg-gray-400 text-white px-2 py-1 rounded cancel-btn" data-id="' + escapeHtml(appt.id) + '" disabled style="opacity:0.6;cursor:not-allowed;">Cancel</button>';
            row += '<button class="bg-blue-600 text-white px-2 py-1 rounded complete-btn" data-id="' + escapeHtml(appt.id) + '">Completed</button>';
        } else if (appt.status === 'CANCELED') {
            row += '<button class="bg-gray-400 text-white px-2 py-1 rounded mr-1 approve-btn" data-id="' + escapeHtml(appt.id) + '" disabled style="opacity:0.6;cursor:not-allowed;">Approve</button>';
            row += '<button class="bg-gray-400 text-white px-2 py-1 rounded mr-1 resched-btn" data-id="' + escapeHtml(appt.id) + '" disabled style="opacity:0.6;cursor:not-allowed;">Resched</button>';
            row += '<button class="bg-gray-400 text-white px-2 py-1 rounded cancel-btn" data-id="' + escapeHtml(appt.id) + '" disabled style="opacity:0.6;cursor:not-allowed;">Cancel</button>';
        } else {
            row += '<button class="bg-green-500 text-white px-2 py-1 rounded mr-1 approve-btn" data-id="' + escapeHtml(appt.id) + '">Approve</button>';
            row += '<button class="bg-yellow-500 text-white px-2 py-1 rounded mr-1 resched-btn" data-id="' + escapeHtml(appt.id) + '">Resched</button>';
            row += '<button class="bg-red-500 text-white px-2 py-1 rounded cancel-btn" data-id="' + escapeHtml(appt.id) + '">Cancel</button>';
        }
        row += '</td>';
        row += '</tr>';
        tbody.innerHTML += row;
    });
    bindActionButtons();
}

function escapeHtml(text) {
    if (typeof text !== 'string') return text;
    return text.replace(/[&<>"']/g, function(m) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[m];
    });
}

function formatDate(dt) {
    if (!dt) return '';
    const d = new Date(dt);
    if (isNaN(d)) return dt;
    return d.toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

// Fetch appointments and update summary bar
function fetchAppointments(search, status) {
    const searchVal = search.trim();
    const statusVal = status;
    let postData = {};
    if (searchVal === '') {
        postData = {search: '', status: statusVal};
    } else {
        postData = {search: searchVal, status: statusVal};
    }
    ajaxPost('/IDSystem/admin/appointments/list', postData, (resp, code) => {
        if (code === 200) {
            try {
                const data = JSON.parse(resp);
                renderAppointmentsTable(data);
                updateSummaryBar(data);
            } catch (e) {
                renderAppointmentsTable([]);
                updateSummaryBar([]);
            }
        } else {
            renderAppointmentsTable([]);
            updateSummaryBar([]);
        }
    });
}

// Update summary bar counts
function updateSummaryBar(appointments) {
    let total = 0, pending = 0, approved = 0, completed = 0;
    if (Array.isArray(appointments)) {
        total = appointments.length;
        appointments.forEach(appt => {
            if (appt.status === 'PENDING') pending++;
            else if (appt.status === 'APPROVED') approved++;
            else if (appt.status === 'COMPLETED') completed++;
        });
    }
    document.getElementById('summary-total').textContent = total;
    document.getElementById('summary-pending').textContent = pending;
    document.getElementById('summary-approved').textContent = approved;
    document.getElementById('summary-completed').textContent = completed;
}

// Bind action buttons
function bindActionButtons() {
        // Completed
        document.querySelectorAll('.complete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('Mark this appointment as completed?')) return;
                ajaxPost('/IDSystem/admin/appointments/complete', {id: this.dataset.id}, (resp, status) => {
                    if (status === 200) fetchAppointments(searchInput.value, statusSelect.value);
                    else alert('Failed to mark as completed.');
                });
            });
        });
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
            if (status === 200) fetchAppointments(searchInput.value, statusSelect.value);
            else alert('Failed to reschedule.');
        });
    });
    // Approve
    document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Approve this appointment?')) return;
            ajaxPost('/IDSystem/admin/appointments/approve', {id: this.dataset.id}, (resp, status) => {
                if (status === 200) fetchAppointments(searchInput.value, statusSelect.value);
                else alert('Failed to approve.');
            });
        });
    });
    // Cancel
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Cancel this appointment?')) return;
            ajaxPost('/IDSystem/admin/appointments/cancel', {id: this.dataset.id}, (resp, status) => {
                if (status === 200) fetchAppointments(searchInput.value, statusSelect.value);
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
}

// Instant search/filter
const searchInput = document.getElementById('search-input');
const statusSelect = document.getElementById('status-select');
searchInput.addEventListener('input', function() {
    fetchAppointments(searchInput.value, statusSelect.value);
});
statusSelect.addEventListener('change', function() {
    fetchAppointments(searchInput.value, statusSelect.value);
});

// Initial load
fetchAppointments(searchInput.value, statusSelect.value);
</script>
