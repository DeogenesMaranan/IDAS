import { ajaxGet, ajaxPost } from './_http_helpers.js';
import { initReschedModal } from './resched_modal.js';
import { createActionButton } from './components/ActionButton.js';
import { createTableRow } from './components/TableRow.js';

// Lightweight toast helper used by this SPA script
function showNotification(message, type = 'info') {
    try {
        const stackClass = 'toast-stack';
        let stack = document.querySelector('.' + stackClass);
        if (!stack) {
            stack = document.createElement('div');
            stack.className = 'toast-stack fixed top-4 right-4 z-50 flex flex-col gap-3 pointer-events-none';
            document.body.appendChild(stack);
        }
        const toast = document.createElement('div');
        const borderClass = type === 'success' ? 'border-green-500' : (type === 'error' ? 'border-red-500' : 'border-blue-500');
        toast.className = `toast text-lg opacity-0 -translate-y-2 transform transition-opacity transition-transform duration-200 max-w-sm rounded-md px-4 py-3 shadow-xl pointer-events-auto bg-white text-gray-800 ${borderClass}`;
        toast.setAttribute('role','status');
        toast.setAttribute('aria-live','polite');
        toast.innerHTML = `<div class="flex items-center gap-3"><div class="flex-1 pr-2">${String(message)}</div><button type="button" class="toast-close ml-2 text-gray-600 hover:text-gray-800 p-1 rounded focus:outline-none focus:ring-1 focus:ring-gray-200" aria-label="Close" title="Close">&times;</button></div>`;
        stack.appendChild(toast);
        // enter animation
        requestAnimationFrame(() => toast.classList.add('opacity-100','translate-y-0'));
        // auto remove
        const VISIBLE_DURATION = 5000;
        const EXIT_ANIM_MS = 250;
        const autoClose = setTimeout(() => {
            toast.classList.remove('opacity-100','translate-y-0');
            setTimeout(() => { if (toast && toast.parentNode) toast.parentNode.removeChild(toast); }, EXIT_ANIM_MS);
        }, VISIBLE_DURATION);
        const btn = toast.querySelector('.toast-close');
        if (btn) btn.addEventListener('click', () => {
            clearTimeout(autoClose);
            toast.classList.remove('opacity-100','translate-y-0');
            setTimeout(() => { if (toast && toast.parentNode) toast.parentNode.removeChild(toast); }, EXIT_ANIM_MS);
        });
    } catch (e) { console.warn('showNotification error', e); }
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
function fetchAppointments(search = '', status = '', dateFrom = '', dateTo = '') {
    clearTimeout(fetchTimeout);
    fetchTimeout = setTimeout(() => {
        const searchVal = String(search || '').trim();
        // convert date-only inputs to inclusive datetimes expected by server
        const df = dateFrom ? (dateFrom.length === 10 ? `${dateFrom} 00:00:00` : dateFrom) : '';
        const dt = dateTo ? (dateTo.length === 10 ? `${dateTo} 23:59:59` : dateTo) : '';
        const postData = { search: searchVal, status: status, date_from: df, date_to: dt };
        ajaxPost('/IDSystem/admin/appointments/list', postData, (resp, code) => {
            // response handled below
            if (code === 200) {
                try {
                    const parsed = JSON.parse(resp);
                    let data = parsed;
                    // Support client-side 'MISSED' filter: appointments that were approved
                    // but not completed and scheduled before today
                    if (status === 'MISSED') {
                        const today = new Date();
                        today.setHours(0,0,0,0);
                        if (Array.isArray(parsed)) {
                            data = parsed.filter(appt => {
                                if (!appt) return false;
                                if (appt.status !== 'APPROVED') return false;
                                const s = appt.scheduled_at || appt.scheduledAt || appt.date || '';
                                const sd = new Date(s);
                                if (isNaN(sd.getTime())) return false;
                                return sd < today;
                            });
                        } else {
                            data = [];
                        }
                    }
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

function initAppointments() {
    const exportBtn = document.getElementById('export-excel-btn');
    const searchInput = document.getElementById('search-input');
    const statusSelect = document.getElementById('status-select');
    const dateFromInput = document.getElementById('date-from');
    const dateToInput = document.getElementById('date-to');

    if (dateFromInput && !dateFromInput.value) {
        dateFromInput.value = new Date().toISOString().slice(0, 10);
    }

    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            const search = (searchInput && searchInput.value) || '';
            const status = (statusSelect && statusSelect.value) || '';
            const dateFrom = (dateFromInput && dateFromInput.value) || '';
            const dateTo = (dateToInput && dateToInput.value) || '';
            const params = new URLSearchParams({ search, status, date_from: dateFrom, date_to: dateTo });
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
    }

    // initialize reschedule modal behavior
    try {
        initReschedModal();
    } catch (e) {
        console.warn('Resched modal init failed', e);
    }
    document.addEventListener('resched:success', () => {
        showNotification('Appointment rescheduled successfully!', 'success');
        fetchAppointments((searchInput && searchInput.value) || '', (statusSelect && statusSelect.value) || '', (dateFromInput && dateFromInput.value) || '', (dateToInput && dateToInput.value) || '');
    });

    // wire inputs (guard in case elements missing)
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            fetchAppointments((searchInput && searchInput.value) || '', (statusSelect && statusSelect.value) || '', (dateFromInput && dateFromInput.value) || '', (dateToInput && dateToInput.value) || '');
        });
    }
    if (statusSelect) {
        statusSelect.addEventListener('change', () => {
            fetchAppointments((searchInput && searchInput.value) || '', (statusSelect && statusSelect.value) || '', (dateFromInput && dateFromInput.value) || '', (dateToInput && dateToInput.value) || '');
        });
    }
    if (dateFromInput) {
        dateFromInput.addEventListener('change', () => {
            if (dateToInput && dateToInput.value && dateFromInput.value > dateToInput.value) {
                dateToInput.value = dateFromInput.value;
            }
            fetchAppointments((searchInput && searchInput.value) || '', (statusSelect && statusSelect.value) || '', (dateFromInput && dateFromInput.value) || '', (dateToInput && dateToInput.value) || '');
        });
    }
    if (dateToInput) {
        dateToInput.addEventListener('change', () => {
            fetchAppointments((searchInput && searchInput.value) || '', (statusSelect && statusSelect.value) || '', (dateFromInput && dateFromInput.value) || '', (dateToInput && dateToInput.value) || '');
        });
    }

    // initial fetch
    fetchAppointments((searchInput && searchInput.value) || '', (statusSelect && statusSelect.value) || '', (dateFromInput && dateFromInput.value) || '', (dateToInput && dateToInput.value) || '');
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const s = document.getElementById('search-input');
            if (s) s.focus();
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAppointments);
} else {
    initAppointments();
}

function bindActionButtons() {
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
                    fetchAppointments(
                        document.getElementById('search-input')?.value || '',
                        document.getElementById('status-select')?.value || '',
                        document.getElementById('date-from')?.value || '',
                        document.getElementById('date-to')?.value || ''
                    );
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
                            fetchAppointments(
                                document.getElementById('search-input')?.value || '',
                                document.getElementById('status-select')?.value || '',
                                document.getElementById('date-from')?.value || '',
                                document.getElementById('date-to')?.value || ''
                            );
                } else {
                    alert('Failed to mark as completed.');
                }
            });
        });
    });
    document.querySelectorAll('.resched-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const ev = new CustomEvent('resched:open', { detail: { id: this.dataset.id } });
            document.dispatchEvent(ev);
        });
    });
    // reschedule modal handled in resched_modal.js via 'resched:open' events
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Cancel this appointment?')) return;
            ajaxPost('/IDSystem/admin/appointments/cancel', { id: this.dataset.id }, (resp, status) => {
                if (status === 200) {
                    showNotification('Appointment canceled successfully!', 'info');
                    fetchAppointments(
                        document.getElementById('search-input')?.value || '',
                        document.getElementById('status-select')?.value || '',
                        document.getElementById('date-from')?.value || '',
                        document.getElementById('date-to')?.value || ''
                    );
                } else {
                    alert('Failed to cancel appointment.');
                }
            });
        });
    });
}