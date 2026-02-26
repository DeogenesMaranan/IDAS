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
    
    const columns = [
        {
            key: 'id',
            className: 'px-6 py-4 text-sm font-medium text-slate-900',
            render: (val) => `#${val}`
        },
        {
            key: 'full_name',
            className: 'px-6 py-4 text-sm text-slate-700',
            render: (val, row) => {
                const nameDiv = document.createElement('div');
                nameDiv.className = 'flex items-center gap-2';
                const avatarDiv = document.createElement('div');
                avatarDiv.className = 'w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white text-xs font-bold';
                avatarDiv.textContent = val?.charAt(0) || '?';
                const nameSpan = document.createElement('span');
                nameSpan.textContent = val;
                nameDiv.appendChild(avatarDiv);
                nameDiv.appendChild(nameSpan);
                return nameDiv;
            }
        },
        {
            key: 'department',
            className: 'px-6 py-4 text-sm text-slate-700',
        },
        {
            key: 'scheduled_at',
            className: 'px-6 py-4 text-sm text-slate-700',
            render: (val) => {
                const dateDiv = document.createElement('div');
                dateDiv.className = 'flex items-center gap-2';
                const dateIcon = document.createElement('i');
                dateIcon.className = 'fas fa-calendar-alt text-slate-400 text-xs';
                dateDiv.appendChild(dateIcon);
                dateDiv.appendChild(document.createTextNode(formatDate(val)));
                return dateDiv;
            }
        },
        {
            key: 'reason',
            className: 'px-6 py-4 text-sm text-slate-700',
        },
        {
            key: 'status',
            className: 'px-6 py-4 text-sm',
            render: (val, row) => {
                const temp = document.createElement('div');
                temp.innerHTML = getStatusBadge(val);
                return temp.firstChild;
            }
        },
        {
            key: 'actions',
            className: 'px-6 py-4',
            render: (val, row) => {
                const actionsDiv = document.createElement('div');
                actionsDiv.className = 'flex items-center justify-center gap-1.5';
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = generateActionButtons(row);
                Array.from(tempDiv.childNodes).forEach(node => actionsDiv.appendChild(node));
                return actionsDiv;
            }
        }
    ];

    appointments.forEach(appt => {
        const rowData = { ...appt, actions: '' };
        const row = createTableRow({ data: rowData, columns });
        tbody.appendChild(row);
    });
    bindActionButtons();
}

import { createActionButton } from './components/ActionButton.js';
import { createTableRow } from './components/TableRow.js';

function generateActionButtons(appt) {
    const fragment = document.createDocumentFragment();
    const viewBtn = createActionButton({
        iconClass: 'fa-eye',
        bgClass: 'bg-blue-500',
        hoverClass: 'hover:bg-blue-600',
        title: 'View Details',
        extraClass: 'view-btn',
        dataId: appt.id,
        disabled: appt.status === 'CANCELED',
    });
    fragment.appendChild(viewBtn);

    if (appt.status === 'PENDING' || appt.status === 'RESCHEDULED') {
        const approveBtn = createActionButton({
            iconClass: 'fa-check',
            bgClass: 'bg-emerald-500',
            hoverClass: 'hover:bg-emerald-600',
            title: 'Approve Appointment',
            extraClass: 'approve-btn',
            dataId: appt.id,
        });
        fragment.appendChild(approveBtn);
    } else if (appt.status === 'APPROVED') {
        const completeBtn = createActionButton({
            iconClass: 'fa-check-double',
            bgClass: 'bg-purple-500',
            hoverClass: 'hover:bg-purple-600',
            title: 'Mark as Completed',
            extraClass: 'complete-btn',
            dataId: appt.id,
        });
        fragment.appendChild(completeBtn);
    }

    if (appt.status === 'PENDING' || appt.status === 'APPROVED' || appt.status === 'RESCHEDULED') {
        const reschedBtn = createActionButton({
            iconClass: 'fa-calendar-alt',
            bgClass: 'bg-amber-500',
            hoverClass: 'hover:bg-amber-600',
            title: 'Reschedule Appointment',
            extraClass: 'resched-btn',
            dataId: appt.id,
        });
        fragment.appendChild(reschedBtn);
    }

    if (appt.status !== 'CANCELED' && appt.status !== 'COMPLETED') {
        const cancelBtn = createActionButton({
            iconClass: 'fa-times',
            bgClass: 'bg-red-500',
            hoverClass: 'hover:bg-red-600',
            title: 'Cancel Appointment',
            extraClass: 'cancel-btn',
            dataId: appt.id,
        });
        fragment.appendChild(cancelBtn);
    }
    const tempDiv = document.createElement('div');
    tempDiv.appendChild(fragment);
    return tempDiv.innerHTML;
}

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
    ['total', 'pending', 'approved', 'completed'].forEach(type => {
        const element = document.getElementById(`summary-${type}`);
        if (element) {
            element.style.transform = 'scale(1.1)';
            element.textContent = counts[type];
            setTimeout(() => element.style.transform = 'scale(1)', 200);
        }
    });
}

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

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('export-excel-btn').addEventListener('click', function() {
        const search = searchInput.value;
        const status = statusSelect.value;
        const params = new URLSearchParams({ search, status });
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
        btn.disabled = true;
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = '/IDSystem/admin/appointments/export-excel?' + params.toString();
        document.body.appendChild(iframe);
        setTimeout(() => {
            document.body.removeChild(iframe);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 2000);
    });
});

function bindActionButtons() {
    const reschedModal = document.getElementById('resched-modal');
    const closeReschedModal = document.getElementById('close-resched-modal');
    const reschedForm = document.getElementById('resched-form');
    const reschedIdInput = document.getElementById('resched-appt-id');
    const dateInput = document.getElementById('resched-date');
    const timeInput = document.getElementById('resched-time');
    if (dateInput) {
        dateInput.min = new Date().toISOString().split('T')[0];
    }
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
    document.querySelectorAll('.resched-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            reschedIdInput.value = this.dataset.id;
            reschedModal.classList.remove('hidden');
            const nextHour = new Date();
            nextHour.setHours(nextHour.getHours() + 1, 0, 0, 0);
            if (timeInput) {
                timeInput.value = nextHour.toTimeString().slice(0, 5);
            }
        });
    });
    closeReschedModal.addEventListener('click', () => {
        reschedModal.classList.add('hidden');
        reschedForm.reset();
    });
    reschedModal.addEventListener('click', (e) => {
        if (e.target === reschedModal) {
            reschedModal.classList.add('hidden');
            reschedForm.reset();
        }
    });
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

function showNotification(message, type = 'info') {
    console.log(`[${type.toUpperCase()}] ${message}`);
}

const searchInput = document.getElementById('search-input');
const statusSelect = document.getElementById('status-select');
searchInput.addEventListener('input', () => {
    fetchAppointments(searchInput.value, statusSelect.value);
});
statusSelect.addEventListener('change', () => {
    fetchAppointments(searchInput.value, statusSelect.value);
});

fetchAppointments(searchInput.value, statusSelect.value);

document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        searchInput.focus();
    }
});