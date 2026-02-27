// Reschedule modal: calendar, counts and time-slot selection
import { ajaxGet, ajaxPost } from './_http_helpers.js';

function formatMonthTitle(date) {
    return new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' }).format(date);
}

function startOfMonth(date) {
    return new Date(date.getFullYear(), date.getMonth(), 1);
}

function endOfMonth(date) {
    return new Date(date.getFullYear(), date.getMonth() + 1, 0);
}

function fetchDailyCounts(start, days, cb) {
    const params = new URLSearchParams({ start, days: String(days) });
    ajaxGet('/IDSystem/admin/appointments/daily-counts?' + params.toString(), (resp, code) => {
        if (code === 200) {
            try {
                const data = JSON.parse(resp);
                cb(null, data);
            } catch (e) {
                cb(new Error('Invalid JSON'));
            }
        } else {
            cb(new Error('Failed to fetch counts'));
        }
    });
}

function renderCalendarGrid(container, monthDate, countsMap, selectedDate, onSelect) {
    container.innerHTML = '';
    const first = startOfMonth(monthDate);
    const last = endOfMonth(monthDate);
    const startWeekday = first.getDay();
    for (let i = 0; i < startWeekday; i++) {
        const cell = document.createElement('div');
        cell.className = 'h-20';
        container.appendChild(cell);
    }
    const today = new Date();
    const todayIso = today.toISOString().slice(0,10);
    for (let d = 1; d <= last.getDate(); d++) {
        const dt = new Date(monthDate.getFullYear(), monthDate.getMonth(), d);
        const cell = document.createElement('button');
        cell.type = 'button';
        cell.className = 'h-20 p-2 rounded-lg text-left w-full';
        const iso = dt.toISOString().slice(0,10);
        const isPast = iso < todayIso;
        const weekday = dt.getDay();
        const isWeekend = weekday === 0 || weekday === 6; // Sunday=0, Saturday=6
        if (isPast || isWeekend) {
            // visually disable past dates and weekends
            cell.className += ' text-slate-300 bg-slate-50 cursor-not-allowed';
            cell.disabled = true;
        } else {
            cell.className += ' bg-slate-50 hover:bg-slate-100';
        }
        if (selectedDate === iso) {
            cell.className += ' ring-2 ring-blue-400';
        }
        const top = document.createElement('div');
        top.className = 'flex items-center justify-between';
        top.innerHTML = `<span class="font-semibold">${d}</span>`;
        const count = countsMap && countsMap[iso] ? countsMap[iso] : 0;
        const badge = document.createElement('span');
        badge.className = 'text-xs text-slate-500';
        badge.textContent = count > 0 ? count : '';
        top.appendChild(badge);
        cell.appendChild(top);
        const hint = document.createElement('div');
        hint.className = 'text-xs text-slate-400 mt-1';
        // show weekend indicator when applicable
        hint.textContent = isWeekend ? 'Weekend' : '';
        cell.appendChild(hint);
        if (!cell.disabled) {
            cell.addEventListener('click', () => onSelect(iso));
        }
        container.appendChild(cell);
    }
}

function formatTimeLabel(hhmm) {
    if (!hhmm) return '';
    const parts = hhmm.split(':');
    if (parts.length < 2) return hhmm;
    const d = new Date();
    d.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10), 0, 0);
    return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}

function buildTimeSlots(container, selectedDate, timeInput) {
    container.innerHTML = '';
    const slots = [];
    // hours from 08:00 to 16:00 inclusive, 1-hour intervals
    for (let h = 8; h <= 16; h++) {
        slots.push((h < 10 ? '0' + h : h) + ':00');
    }
    // slotCountsMap and currentScheduledTime may be attached to container dataset
    const slotCountsMap = container._slotCountsMap || {};
    const currentScheduledTime = container._currentScheduledTime || null;
    slots.forEach(s => {
        const count = slotCountsMap[s] || 0;
        const btn = document.createElement('button');
        btn.type = 'button';
        // make time buttons match calendar cell sizing and layout
        btn.className = 'h-20 p-2 rounded-lg text-left w-full bg-slate-50 hover:bg-slate-100';
        btn.innerHTML = `<div class="flex items-center justify-between w-full"><span class="font-semibold">${formatTimeLabel(s)}</span><span class="text-xs text-slate-500">${count > 0 ? count : ''}</span></div>`;

        btn.addEventListener('click', () => {
            timeInput.value = s;
            const sel = document.getElementById('resched-selected-time');
            if (sel) sel.textContent = formatTimeLabel(s);
            container.querySelectorAll('button').forEach(b => b.classList.remove('ring-2','ring-blue-400','bg-slate-100'));
            btn.classList.add('ring-2','ring-blue-400','bg-slate-100');
        });

        // if this is the appointment's current scheduled time, mark it selected by default
        if (currentScheduledTime && currentScheduledTime === s) {
            btn.classList.add('ring-2','ring-blue-400','bg-slate-100');
            if (timeInput) timeInput.value = s;
            const sel = document.getElementById('resched-selected-time');
            if (sel) sel.textContent = formatTimeLabel(s);
        }

        container.appendChild(btn);
    });
}

function fetchSlotCounts(date, cb) {
    const params = new URLSearchParams({ date });
    ajaxGet('/IDSystem/admin/appointments/slot-counts?' + params.toString(), (resp, code) => {
        if (code === 200) {
            try {
                const data = JSON.parse(resp);
                cb(null, data);
            } catch (e) {
                cb(new Error('Invalid JSON'));
            }
        } else {
            cb(new Error('Failed to fetch slot counts'));
        }
    });
}

export function initReschedModal() {
    const reschedModal = document.getElementById('resched-modal');
    const closeReschedModal = document.getElementById('close-resched-modal');
    const reschedForm = document.getElementById('resched-form');
    const reschedIdInput = document.getElementById('resched-appt-id');
    const dateInput = document.getElementById('resched-date');
    const timeInput = document.getElementById('resched-time');
    const calendarGrid = document.getElementById('resched-calendar-grid');
    const monthTitle = document.getElementById('resched-month-title');
    const prevMonthBtn = document.getElementById('resched-prev-month');
    const nextMonthBtn = document.getElementById('resched-next-month');
    const timeSlotsContainer = document.getElementById('resched-time-slots');

    let currentMonth = startOfMonth(new Date());
    let countsMap = {};
    let selectedDateIso = null;

    function refreshCalendar() {
        if (!calendarGrid) return;
        monthTitle.textContent = formatMonthTitle(currentMonth);
        // stable handler so re-rendering preserves click handlers
        const onDateSelect = (iso) => {
            selectedDateIso = iso;
            if (dateInput) dateInput.value = iso;
            // re-render calendar to update selected highlight but keep the same handler
            renderCalendarGrid(calendarGrid, currentMonth, countsMap, selectedDateIso, onDateSelect);
            // fetch slot counts for the selected date then build time slots with counts
            fetchSlotCounts(selectedDateIso, (err, slotMap) => {
                timeSlotsContainer._slotCountsMap = slotMap || {};
                // keep any pre-existing currentScheduledTime
                timeSlotsContainer._currentScheduledTime = timeSlotsContainer._currentScheduledTime || null;
                buildTimeSlots(timeSlotsContainer, selectedDateIso, timeInput);
                // reset selected time display when date changes only if currentScheduledTime is not for this date
                const sel = document.getElementById('resched-selected-time');
                if (sel && (!timeSlotsContainer._currentScheduledTime || (dateInput && dateInput.value !== (timeSlotsContainer._currentScheduledDate || '')))) sel.textContent = '—';
                timeSlotsContainer._currentScheduledDate = dateInput ? dateInput.value : null;
            });
        };

        renderCalendarGrid(calendarGrid, currentMonth, countsMap, selectedDateIso, onDateSelect);
    }

    function loadCountsAndRender() {
        const start = new Date().toISOString().slice(0,10);
        fetchDailyCounts(start, 120, (err, data) => {
            if (!err) countsMap = data; else countsMap = {};
            refreshCalendar();
        });
    }

    prevMonthBtn.addEventListener('click', () => { currentMonth.setMonth(currentMonth.getMonth() - 1); refreshCalendar(); });
    nextMonthBtn.addEventListener('click', () => { currentMonth.setMonth(currentMonth.getMonth() + 1); refreshCalendar(); });

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
                // dispatch custom event so caller can refresh list
                const ev = new CustomEvent('resched:success');
                document.dispatchEvent(ev);
                reschedModal.classList.add('hidden');
                reschedForm.reset();
            } else {
                alert('Failed to reschedule appointment.');
            }
        });
    });

    function openReschedModal(id) {
        reschedIdInput.value = id;
        reschedModal.classList.remove('hidden');
        selectedDateIso = null;
        if (dateInput) dateInput.value = '';
        // fetch current appointment details to pre-select its scheduled date/time
        ajaxPost('/IDSystem/admin/appointments/view', { id }, (resp, status) => {
            if (status === 200) {
                try {
                    const appt = JSON.parse(resp);
                    const sched = (appt.scheduled_at || '').trim();
                    if (sched) {
                        const parts = sched.split(' ');
                        const adate = parts[0];
                        const atime = (parts[1] || '').slice(0,5);
                        if (adate) {
                            selectedDateIso = adate;
                            if (dateInput) dateInput.value = adate;
                            currentMonth = startOfMonth(new Date(adate));
                        }
                        if (atime) {
                            // normalize time to HH:MM with leading zero
                            const partsT = atime.split(':');
                            const hh = partsT[0] ? partsT[0].padStart(2, '0') : '08';
                            const mm = partsT[1] ? partsT[1].padStart(2, '0') : '00';
                            const norm = hh + ':' + mm;
                            // store current scheduled time so we can highlight it after slot counts load
                            timeSlotsContainer._currentScheduledTime = norm;
                            timeSlotsContainer._currentScheduledDate = adate;
                        }
                    }
                } catch (e) {
                    // ignore parse errors and fall back to default
                }
            }
            // Determine target date for slots (appt date or today)
            const targetDate = selectedDateIso || new Date().toISOString().slice(0,10);
            // fetch slot counts immediately and build slots so they appear even if calendar is still loading
            fetchSlotCounts(targetDate, (err, slotMap) => {
                timeSlotsContainer._slotCountsMap = slotMap || {};
                // if no current scheduled time, set a sensible default
                if (!timeSlotsContainer._currentScheduledTime) {
                    const nextHour = new Date();
                    nextHour.setHours(nextHour.getHours() + 1, 0, 0, 0);
                    let defaultHour = nextHour.getHours();
                    if (defaultHour < 8) defaultHour = 8;
                    if (defaultHour > 16) defaultHour = 8;
                    const defaultTime = (defaultHour < 10 ? '0' + defaultHour : defaultHour) + ':00';
                    if (timeInput) timeInput.value = defaultTime;
                    const sel = document.getElementById('resched-selected-time');
                    if (sel) sel.textContent = formatTimeLabel(defaultTime);
                } else {
                    if (timeInput) timeInput.value = timeSlotsContainer._currentScheduledTime;
                    const sel = document.getElementById('resched-selected-time');
                    if (sel) sel.textContent = formatTimeLabel(timeSlotsContainer._currentScheduledTime);
                }
                // ensure current scheduled date marker is set
                timeSlotsContainer._currentScheduledDate = timeSlotsContainer._currentScheduledDate || (selectedDateIso || null);
                buildTimeSlots(timeSlotsContainer, targetDate, timeInput);
            });

            // still load daily counts and render calendar (async)
            loadCountsAndRender();
        });
    }

    // also respond to dispatched open events so callers don't need direct reference
    document.addEventListener('resched:open', (e) => {
        const id = e?.detail?.id;
        if (id) openReschedModal(id);
    });

    return openReschedModal;
}
