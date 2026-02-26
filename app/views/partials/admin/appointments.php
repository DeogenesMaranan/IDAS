<div class="space-y-6">

    <!-- Header Section with Gradient -->
    <div class="relative overflow-hidden">
        <div></div>
        <div class="relative px-6 py-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">Appointment Management</h1>
            <p class="text-slate-600 text-lg mt-1 flex items-center gap-2">
                View and manage all ID appointment requests
            </p>
        </div>
    </div>

    <!-- Enhanced Summary Cards -->
    <div id="summary-bar" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Card -->
        <div class="group rounded-2xl p-6 flex items-center justify-between shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100 hover:border-slate-200">
            <div>
                <span class="text-sm font-medium text-blue-500 uppercase tracking-wider">Total</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span id="summary-total" class="summary-number text-4xl font-bold text-blue-800">0</span>
                    <span class="text-sm text-blue-400">appointments</span>
                </div>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-calendar text-blue-600 text-xl"></i>
            </div>
        </div>
        
        <!-- Pending Card -->
        <div class="group rounded-2xl p-6 flex items-center justify-between shadow-lg hover:shadow-xl transition-all duration-300 border border-amber-100 hover:border-amber-200">
            <div>
                <span class="text-sm font-medium text-amber-600 uppercase tracking-wider">Pending</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span id="summary-pending" class="summary-number text-4xl font-bold text-amber-700">0</span>
                    <span class="text-sm text-amber-500">awaiting</span>
                </div>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-clock text-amber-600 text-xl"></i>
            </div>
        </div>
        
        <!-- Approved Card -->
        <div class="group rounded-2xl p-6 flex items-center justify-between shadow-lg hover:shadow-xl transition-all duration-300 border border-emerald-100 hover:border-emerald-200">
            <div>
                <span class="text-sm font-medium text-emerald-600 uppercase tracking-wider">Approved</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span id="summary-approved" class="summary-number text-4xl font-bold text-emerald-700">0</span>
                    <span class="text-sm text-emerald-500">confirmed</span>
                </div>
            </div>
            <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
            </div>
        </div>
        
        <!-- Completed Card -->
        <div class="group rounded-2xl p-6 flex items-center justify-between shadow-lg hover:shadow-xl transition-all duration-300 border border-blue-100 hover:border-blue-200">
            <div>
                <span class="text-sm font-medium text-slate-600 uppercase tracking-wider">Completed</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span id="summary-completed" class="summary-number text-4xl font-bold text-slate-700">0</span>
                    <span class="text-sm text-slate-500">finished</span>
                </div>
            </div>
            <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-check-double text-slate-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white/80 backdrop-blur-sm p-5 rounded-2xl shadow-lg border border-slate-100 flex flex-wrap gap-4 items-center">
        <div class="relative flex-1 min-w-[280px]">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input
                type="text"
                id="search-input"
                placeholder="Search by name or reference number..."
                value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all bg-white/70"
            />
        </div>

        <div class="relative min-w-[200px]">
            <i class="fas fa-filter absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 z-10"></i>
            <select
                id="status-select"
                class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none appearance-none bg-white/70 cursor-pointer"
            >
                <option value="">All Status</option>
                <option value="APPROVED">Approved</option>
                <option value="PENDING">Pending</option>
                <option value="RESCHEDULED">Rescheduled</option>
                <option value="CANCELED">Canceled</option>
                <option value="COMPLETED">Completed</option>
            </select>
        </div>

        <button
            id="export-excel-btn"
            class="ml-auto bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white px-6 py-3 rounded-xl flex items-center gap-3 shadow-lg hover:shadow-xl transition-all duration-200 font-medium"
        >
            <i class="fas fa-file-excel text-lg"></i>
            <span>Export</span>
        </button>
    </div>

    <!-- Enhanced Table with Modern Design -->
    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-gradient-to-r from-slate-50 to-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Ref #</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">ID Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white"></tbody>
            </table>
        </div>
    </div>

    <!-- Enhanced Reschedule Modal -->
    <div id="resched-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl relative animate-fadeIn">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 rounded-t-2xl">
                <button id="close-resched-modal" class="absolute top-4 right-4 text-white/80 hover:text-white text-3xl transition-colors">&times;</button>
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i>
                    Reschedule Appointment
                </h2>
                <p class="text-blue-100 text-sm mt-1">Select a new date and time for the appointment</p>
            </div>
            
            <!-- Modal Body -->
            <form id="resched-form" class="p-6 space-y-5">
                <input type="hidden" id="resched-appt-id">

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                        <i class="fas fa-calendar-day text-blue-500"></i>
                        Select Date
                    </label>
                    <div class="relative">
                        <i class="fas fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" id="resched-date" required 
                               class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                        <i class="fas fa-clock text-blue-500"></i>
                        Select Time
                    </label>
                    <div class="relative">
                        <i class="fas fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="time" id="resched-time" required 
                               class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-3.5 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 mt-6">
                    <i class="fas fa-save"></i>
                    Save Changes
                </button>
            </form>
        </div>
    </div>

</div>

<script>
// Utility function for AJAX POST requests
function ajaxPost(url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onload = function() {
        callback(xhr.responseText, xhr.status);
    };
    
    xhr.onerror = function() {
        callback(null, 500);
    };
    
    xhr.send(new URLSearchParams(data).toString());
}

// Utility function for AJAX GET requests
function ajaxGet(url, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onload = function() {
        callback(xhr.responseText, xhr.status);
    };
    
    xhr.onerror = function() {
        callback(null, 500);
    };
    
    xhr.send();
}

// Enhanced HTML escaping
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Enhanced date formatting
function formatDate(dateString) {
    if (!dateString) return '—';
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        return new Intl.DateTimeFormat('en-US', {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        }).format(date);
    } catch (e) {
        return dateString;
    }
}

// Get status badge HTML with inline classes
function getStatusBadge(status) {
    const badges = {
        'PENDING': '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 border border-amber-200"><i class="fas fa-clock mr-1 text-xs"></i>Pending</span>',
        'APPROVED': '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200"><i class="fas fa-check-circle mr-1 text-xs"></i>Approved</span>',
        'RESCHEDULED': '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 border border-blue-200"><i class="fas fa-calendar-alt mr-1 text-xs"></i>Rescheduled</span>',
        'CANCELED': '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200"><i class="fas fa-times-circle mr-1 text-xs"></i>Canceled</span>',
        'COMPLETED': '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 border border-purple-200"><i class="fas fa-check-double mr-1 text-xs"></i>Completed</span>'
    };
    return badges[status] || '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">Unknown</span>';
}

// Render appointments table with enhanced design
function renderAppointmentsTable(appointments) {
    const tbody = document.querySelector('table tbody');
    tbody.innerHTML = '';
    
    if (!appointments || appointments.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center text-slate-400">
                        <i class="fas fa-calendar-times text-5xl mb-3"></i>
                        <p class="text-lg font-medium">No appointments found</p>
                        <p class="text-sm">Try adjusting your search or filter criteria</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    appointments.forEach(appt => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-slate-50 transition-colors';
        
        row.innerHTML = `
            <td class="px-6 py-4 text-sm font-medium text-slate-900">#${escapeHtml(appt.id)}</td>
            <td class="px-6 py-4 text-sm text-slate-700">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                        ${escapeHtml(appt.full_name?.charAt(0) || '?')}
                    </div>
                    <span>${escapeHtml(appt.full_name)}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-slate-700">${escapeHtml(appt.department)}</td>
            <td class="px-6 py-4 text-sm text-slate-700">
                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-slate-400 text-xs"></i>
                    ${escapeHtml(formatDate(appt.scheduled_at))}
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-slate-700">${escapeHtml(appt.reason)}</td>
            <td class="px-6 py-4 text-sm">${getStatusBadge(appt.status)}</td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-1.5">
                    ${generateActionButtons(appt)}
                </div>
            </td>
        `;
        
        tbody.appendChild(row);
    });
    
    bindActionButtons();
}

// Generate action buttons based on status with improved inline Tailwind classes
function generateActionButtons(appt) {
    let buttons = '';
    
    // View button (always enabled except CANCELED)
    const viewDisabled = appt.status === 'CANCELED' ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '';
    buttons += `<button class="view-btn inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 hover:bg-blue-600 text-white shadow-sm hover:shadow-md transition-all duration-200 text-sm" data-id="${escapeHtml(appt.id)}" ${viewDisabled} title="View Details">
        <i class="fas fa-eye"></i>
    </button>`;
    
    // Approve or Complete button
    if (appt.status === 'PENDING' || appt.status === 'RESCHEDULED') {
        buttons += `<button class="approve-btn inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm hover:shadow-md transition-all duration-200 text-sm" data-id="${escapeHtml(appt.id)}" title="Approve Appointment">
            <i class="fas fa-check"></i>
        </button>`;
    } else if (appt.status === 'APPROVED') {
        buttons += `<button class="complete-btn inline-flex items-center justify-center w-8 h-8 rounded-lg bg-purple-500 hover:bg-purple-600 text-white shadow-sm hover:shadow-md transition-all duration-200 text-sm" data-id="${escapeHtml(appt.id)}" title="Mark as Completed">
            <i class="fas fa-check-double"></i>
        </button>`;
    }
    
    // Reschedule button
    if (appt.status === 'PENDING' || appt.status === 'APPROVED') {
        buttons += `<button class="resched-btn inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500 hover:bg-amber-600 text-white shadow-sm hover:shadow-md transition-all duration-200 text-sm" data-id="${escapeHtml(appt.id)}" title="Reschedule Appointment">
            <i class="fas fa-calendar-alt"></i>
        </button>`;
    }
    
    // Cancel button
    if (appt.status !== 'CANCELED' && appt.status !== 'COMPLETED') {
        buttons += `<button class="cancel-btn inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500 hover:bg-red-600 text-white shadow-sm hover:shadow-md transition-all duration-200 text-sm" data-id="${escapeHtml(appt.id)}" title="Cancel Appointment">
            <i class="fas fa-times"></i>
        </button>`;
    }
    
    return buttons;
}

// Update summary bar counts with animation
function updateSummaryBar(appointments) {
    const counts = {
        total: appointments?.length || 0,
        pending: 0,
        approved: 0,
        completed: 0
    };
    
    if (Array.isArray(appointments)) {
        appointments.forEach(appt => {
            if (appt.status === 'PENDING') counts.pending++;
            else if (appt.status === 'APPROVED') counts.approved++;
            else if (appt.status === 'COMPLETED') counts.completed++;
        });
    }
    
    // Animate number updates
    ['total', 'pending', 'approved', 'completed'].forEach(type => {
        const element = document.getElementById(`summary-${type}`);
        if (element) {
            element.style.transform = 'scale(1.1)';
            element.textContent = counts[type];
            setTimeout(() => element.style.transform = 'scale(1)', 200);
        }
    });
}

// Fetch appointments with debouncing
let fetchTimeout;
function fetchAppointments(search, status) {
    clearTimeout(fetchTimeout);
    fetchTimeout = setTimeout(() => {
        const searchVal = search.trim();
        const postData = { search: searchVal, status: status };
        
        ajaxPost('/IDSystem/admin/appointments/list', postData, (resp, code) => {
            if (code === 200) {
                try {
                    const data = JSON.parse(resp);
                    renderAppointmentsTable(data);
                    updateSummaryBar(data);
                } catch (e) {
                    console.error('Error parsing response:', e);
                    renderAppointmentsTable([]);
                    updateSummaryBar([]);
                }
            } else {
                console.error('Error fetching appointments:', code);
                renderAppointmentsTable([]);
                updateSummaryBar([]);
            }
        });
    }, 300);
}

// Export to Excel functionality
document.getElementById('export-excel-btn').addEventListener('click', function() {
    const search = searchInput.value;
    const status = statusSelect.value;
    const params = new URLSearchParams({ search, status });
    
    // Show loading state
    const btn = this;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
    btn.disabled = true;
    
    // Trigger download
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = '/IDSystem/admin/appointments/export-excel?' + params.toString();
    document.body.appendChild(iframe);
    
    // Restore button
    setTimeout(() => {
        document.body.removeChild(iframe);
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 2000);
});

// Bind action buttons
function bindActionButtons() {
    const reschedModal = document.getElementById('resched-modal');
    const closeReschedModal = document.getElementById('close-resched-modal');
    const reschedForm = document.getElementById('resched-form');
    const reschedIdInput = document.getElementById('resched-appt-id');
    const dateInput = document.getElementById('resched-date');
    const timeInput = document.getElementById('resched-time');
    
    // Set minimum date to today
    if (dateInput) {
        dateInput.min = new Date().toISOString().split('T')[0];
    }
    
    // View button handler
    document.querySelectorAll('.view-btn:not([disabled])').forEach(btn => {
        btn.addEventListener('click', function() {
            ajaxPost('/IDSystem/admin/appointments/view', { id: this.dataset.id }, (resp, status) => {
                if (status === 200) {
                    try {
                        const data = JSON.parse(resp);
                        let details = 'Appointment Details\n\n';
                        for (const [key, value] of Object.entries(data)) {
                            if (value) {
                                const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                details += `${label}: ${value}\n`;
                            }
                        }
                        alert(details);
                    } catch (e) {
                        alert(resp);
                    }
                } else {
                    alert('Failed to fetch appointment details.');
                }
            });
        });
    });
    
    // Approve button handler
    document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Approve this appointment?')) return;
            
            ajaxPost('/IDSystem/admin/appointments/approve', { id: this.dataset.id }, (resp, status) => {
                if (status === 200) {
                    showNotification('Appointment approved successfully!', 'success');
                    fetchAppointments(searchInput.value, statusSelect.value);
                } else {
                    alert('Failed to approve appointment.');
                }
            });
        });
    });
    
    // Complete button handler
    document.querySelectorAll('.complete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Mark this appointment as completed?')) return;
            
            ajaxPost('/IDSystem/admin/appointments/complete', { id: this.dataset.id }, (resp, status) => {
                if (status === 200) {
                    showNotification('Appointment marked as completed!', 'success');
                    fetchAppointments(searchInput.value, statusSelect.value);
                } else {
                    alert('Failed to mark as completed.');
                }
            });
        });
    });
    
    // Reschedule button handler
    document.querySelectorAll('.resched-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            reschedIdInput.value = this.dataset.id;
            reschedModal.classList.remove('hidden');
            
            // Set default time to next hour
            const nextHour = new Date();
            nextHour.setHours(nextHour.getHours() + 1, 0, 0, 0);
            if (timeInput) {
                timeInput.value = nextHour.toTimeString().slice(0, 5);
            }
        });
    });
    
    // Close modal
    closeReschedModal.addEventListener('click', () => {
        reschedModal.classList.add('hidden');
        reschedForm.reset();
    });
    
    // Click outside modal to close
    reschedModal.addEventListener('click', (e) => {
        if (e.target === reschedModal) {
            reschedModal.classList.add('hidden');
            reschedForm.reset();
        }
    });
    
    // Reschedule form submit
    reschedForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = reschedIdInput.value;
        const date = dateInput.value;
        const time = timeInput.value;
        
        if (!date || !time) {
            alert('Please select both date and time.');
            return;
        }
        
        ajaxPost('/IDSystem/admin/appointments/reschedule', { id, date, time }, (resp, status) => {
            if (status === 200) {
                showNotification('Appointment rescheduled successfully!', 'success');
                reschedModal.classList.add('hidden');
                reschedForm.reset();
                fetchAppointments(searchInput.value, statusSelect.value);
            } else {
                    alert('Failed to reschedule appointment.');
            }
        });
    });
    
    // Cancel button handler
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Cancel this appointment?')) return;
            
            ajaxPost('/IDSystem/admin/appointments/cancel', { id: this.dataset.id }, (resp, status) => {
                if (status === 200) {
                    showNotification('Appointment canceled successfully!', 'info');
                    fetchAppointments(searchInput.value, statusSelect.value);
                } else {
                    alert('Failed to cancel appointment.');
                }
            });
        });
    });
}

// Show notification (simple alert for now, can be enhanced)
function showNotification(message, type = 'info') {
    // You can replace this with a toast notification system
    console.log(`[${type.toUpperCase()}] ${message}`);
}

// Initialize event listeners
const searchInput = document.getElementById('search-input');
const statusSelect = document.getElementById('status-select');

searchInput.addEventListener('input', () => {
    fetchAppointments(searchInput.value, statusSelect.value);
});

statusSelect.addEventListener('change', () => {
    fetchAppointments(searchInput.value, statusSelect.value);
});

// Initial load
fetchAppointments(searchInput.value, statusSelect.value);

// Add keyboard shortcut for search (Ctrl/Cmd + K)
document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        searchInput.focus();
    }
});
</script>