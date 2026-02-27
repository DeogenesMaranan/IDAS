<form id="booking-form" action="/IDSystem/appointments" method="POST" enctype="multipart/form-data">

    <?php
    $displayRole = 'Student';
    if (isset($role)) {
        $r = strtoupper($role);
        if ($r === 'FACULTY' || $r === 'STAFF') $displayRole = 'Faculty';
        elseif ($r === 'STUDENT') $displayRole = 'Student';
    } elseif (isset($_SESSION) && isset($_SESSION['role'])) {
        $r = strtoupper($_SESSION['role']);
        if ($r === 'FACULTY' || $r === 'STAFF') $displayRole = 'Faculty';
        elseif ($r === 'STUDENT') $displayRole = 'Student';
    }
    ?>

    <div class="card p-6 mb-6 rounded-lg bg-white shadow-sm">
        <h1 class="text-3xl font-bold mb-2">Book Appointment for <?php echo htmlspecialchars($displayRole, ENT_QUOTES, 'UTF-8'); ?> ID</h1>
        <p class="text-gray-500 mb-6">Schedule your ID creation or renewal appointment</p>
    </div>

    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-8">
            <div class="card p-6 mb-6 rounded-lg bg-white shadow-sm">

                <h3 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($displayRole, ENT_QUOTES, 'UTF-8'); ?> Information</h3>
                <p class="text-sm text-gray-400 mb-4">Please fill in your personal details</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Full Name *</label>
                        <input id="full_name" name="full_name" required class="w-full bg-gray-50 border rounded px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">ID Number *</label>
                        <input id="student_id" name="student_id" placeholder="e.g., 2023-00001234" required class="w-full bg-gray-50 border rounded px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Email Address *</label>
                        <input id="email" name="email" type="email" required class="w-full bg-gray-50 border rounded px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Course / Grade Level / Strand *</label>
                        <input id="course_grade_strand" name="course_grade_strand" class="w-full bg-gray-50 border rounded px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Department *</label>
                        <input id="department" name="department" class="w-full bg-gray-50 border rounded px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Year Level *</label>
                        <input id="year" name="year" class="w-full bg-gray-50 border rounded px-3 py-2" />
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Purpose of Appointment *</label>
                        <select id="reason" name="reason" required class="w-full bg-gray-50 border rounded px-3 py-2">
                            <option value="">Select purpose</option>
                            <option value="NEW_ID">New ID</option>
                            <option value="RENEW_ID">Renewal</option>
                            <option value="REPLACEMENT">Replacement</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium">2x2 ID Photo</label>
                    <input id="photo" name="photo" type="file" accept="image/*" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="col-span-4">
                <aside class="card p-4 rounded-lg bg-white shadow-sm">
                    <h3 class="font-semibold mb-2">Select Date & Time</h3>
                    <p class="text-sm text-gray-400 mb-4">Choose your preferred appointment date and time</p>

                    <div id="booking-calendar" class="w-full px-2 py-3 border border-slate-200 rounded-xl bg-white text-slate-800">
                        <div class="flex items-center justify-between mb-2">
                            <button type="button" id="booking-prev-month" class="text-slate-600 hover:text-slate-800">&lt;</button>
                            <div id="booking-month-title" class="font-semibold"></div>
                            <button type="button" id="booking-next-month" class="text-slate-600 hover:text-slate-800">&gt;</button>
                        </div>

                        <div class="grid grid-cols-7 gap-1 text-center text-xs text-slate-500 mb-2">
                            <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                        </div>

                        <div id="booking-calendar-grid" class="grid grid-cols-7 gap-2"></div>
                    </div>

                    <div class="mt-4 text-sm text-gray-600">
                        <p class="font-semibold">Legend:</p>
                        <ul class="mt-2">
                            <li><span class="inline-block w-3 h-3 bg-red-800 mr-2 align-middle"></span> Selected Date</li>
                            <li><span class="inline-block w-3 h-3 bg-yellow-400 mr-2 align-middle"></span> Available Dates</li>
                            <li><span class="inline-block w-3 h-3 bg-gray-200 mr-2 align-middle"></span> Unavailable (Weekends/Past)</li>
                        </ul>
                    </div>

                    <div class="mt-4 border border-slate-200 rounded-xl p-4">
                        <div class="text-xs text-slate-500">Selected</div>
                        <div id="booking-selected-time" class="text-lg font-semibold">—</div>
                        <div id="booking-time-slots" class="grid grid-cols-3 gap-2 mt-3"></div>
                        <p id="booking-time-help" class="text-sm text-gray-400 mt-2">Please select a date to view times.</p>
                    </div>

                    <input type="hidden" id="appointment_date" name="appointment_date" />
                    <input type="hidden" id="appointment_time" name="appointment_time" />
                </aside>
        </div>
    </div>

    <div class="mt-6 flex justify-end space-x-2">
        <button type="button" onclick="window.history.back()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Cancel</button>
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Continue</button>
    </div>
</form>
<script>
(function(){
    const form = document.getElementById('booking-form');
    const calendarEl = document.getElementById('booking-calendar');
    const calendarGrid = document.getElementById('booking-calendar-grid');
    const monthTitle = document.getElementById('booking-month-title');
    const prevMonthBtn = document.getElementById('booking-prev-month');
    const nextMonthBtn = document.getElementById('booking-next-month');
    const dateInput = document.getElementById('appointment_date');
    const timeInput = document.getElementById('appointment_time');
    const timeSlotsEl = document.getElementById('booking-time-slots');
    const selectedTimeEl = document.getElementById('booking-selected-time');
    const timeHelp = document.getElementById('booking-time-help');
    const TOAST_KEY = 'booking_success_toast';

    function showToast(message, type = 'success'){
        if(!message) return;
        let stack = document.querySelector('.toast-stack');
        if(!stack){
            stack = document.createElement('div');
            stack.className = 'toast-stack fixed top-4 right-4 z-50 flex flex-col gap-3 pointer-events-none';
            document.body.appendChild(stack);
        }
        const toast = document.createElement('div');
        const isSuccess = type === 'success';
        toast.className = `toast ${isSuccess ? 'toast-success border-green-500' : 'toast-error border-red-500'} text-lg opacity-0 -translate-y-2 transform transition-opacity transition-transform duration-200 max-w-sm rounded-md px-4 py-3 shadow-xl pointer-events-auto bg-white text-gray-800 border-2`;
        toast.innerHTML = `<div class="flex items-center gap-3"><div class="flex-1 pr-2"></div><button type="button" class="toast-close ml-2 text-gray-600 hover:text-gray-800 p-1 rounded focus:outline-none focus:ring-1 focus:ring-gray-200" aria-label="Close" title="Close">&times;</button></div>`;
        toast.querySelector('.flex-1').textContent = message;
        toast.querySelector('.toast-close').addEventListener('click', () => toast.remove());
        stack.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('opacity-100','translate-y-0'));
        setTimeout(() => toast.classList.remove('opacity-100','translate-y-0'), 4200);
        setTimeout(() => toast.remove(), 4700);
    }

    let MAX_AVAILABLE_PER_DAY = 100;
    let MAX_AVAILABLE_PER_SLOT = 100;
    const times = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00'];

    function formatTimeLabel(t24){
        if(!t24) return '';
        const parts = t24.split(':');
        let hour = parseInt(parts[0], 10);
        const minute = parts[1] || '00';
        const ampm = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12;
        if(hour === 0) hour = 12;
        return `${String(hour).padStart(2,'0')}:${minute} ${ampm}`;
    }

    const availabilityByMonth = {};

    async function fetchAvailability(year, monthIndex){
        try{
            const month = String(monthIndex+1).padStart(2,'0');
            const url = `/IDSystem/appointments/availability?year=${year}&month=${month}`;
            const res = await fetch(url, { credentials: 'same-origin' });
            if(!res.ok) return {};
            const data = await res.json();
            if (data && data._meta) {
                if (data._meta.max_per_day !== undefined) MAX_AVAILABLE_PER_DAY = Number(data._meta.max_per_day);
                if (data._meta.max_per_slot !== undefined) MAX_AVAILABLE_PER_SLOT = Number(data._meta.max_per_slot);
                delete data._meta;
            }
            const norm = normalizeAvailability(data, year, monthIndex+1);
            console.debug('availability raw', data);
            console.debug('availability normalized', norm);
            return norm || {};
        }catch(e){
            console.debug('fetchAvailability error', e);
            return {};
        }
    }

    function normalizeAvailability(data, year, month){
        const out = {};
        if(!data) return out;

        if(typeof data === 'object' && !Array.isArray(data)){
            const keys = Object.keys(data);
            if(keys.length === 1 && keys[0].indexOf('-')>0 && Object.keys(data[keys[0]] || {}).length > 0){
                const maybe = data[keys[0]];
                if(typeof maybe === 'object') data = maybe;
            }

            for(const k in data){
                try{
                    const val = data[k];
                    const dateStr = String(k).length ? String(k) : null;
                    if(!dateStr) continue;
                    if(typeof val === 'number'){
                        out[dateStr] = { count: Number(val), times: {} };
                    } else if(typeof val === 'object'){
                        const cnt = Number(val.count || val.total || val.booked || 0);
                        let times = {};
                        if(val.times && typeof val.times === 'object') times = val.times;
                        else if(Array.isArray(val.timeSlots)){
                            val.timeSlots.forEach(ts => { if(ts.time) times[ts.time] = ts.count || ts.c || 0; });
                        } else if(Array.isArray(val.slots)){
                            val.slots.forEach(ts => { if(ts.time) times[ts.time] = ts.count || ts.c || 0; });
                        }
                        out[dateStr] = { count: cnt, times };
                    }
                }catch(e){ continue; }
            }
            return out;
        }

        if(Array.isArray(data)){
            data.forEach(item => {
                try{
                    const date = item.date || item.day || item.d || item.dayString || null;
                    if(!date) return;
                    const dateStr = String(date);
                    const cnt = Number(item.count || item.total || item.booked || item.c || 0);
                    let times = {};
                    if(item.times && typeof item.times === 'object') times = item.times;
                    else if(Array.isArray(item.times)){
                        item.times.forEach(ts => { if(ts.time) times[ts.time] = ts.count || ts.c || 0; });
                    } else if(Array.isArray(item.slots)){
                        item.slots.forEach(ts => { if(ts.time) times[ts.time] = ts.count || ts.c || 0; });
                    }
                    out[dateStr] = { count: cnt, times };
                }catch(e){ }
            });
            return out;
        }

        return out;
    }

    const today = new Date(); today.setHours(0,0,0,0);
    let viewYear = today.getFullYear();
    let viewMonth = today.getMonth();
    let selectedDateValue = dateInput && dateInput.value ? new Date(dateInput.value) : null;

    function formatDate(dateObj){
        const m = String(dateObj.getMonth() + 1).padStart(2, '0');
        const d = String(dateObj.getDate()).padStart(2, '0');
        return `${dateObj.getFullYear()}-${m}-${d}`;
    }

    function sameDay(a, b){
        return a && b && a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
    }

    (function addCalendarStyles(){
        const style = document.createElement('style');
        style.textContent = `
            .cal-cell { border:1px solid #e5e7eb; border-radius:0.375rem; padding:0.8rem 0.35rem 0.4rem 0.35rem; text-align:center; background:#fff; position:relative; min-height:48px; display:flex; align-items:flex-start; justify-content:center; }
            .cal-cell .day-num { display:block; margin-top:6px; font-weight:600; }
            .cal-cell.disabled { background:#f3f4f6; color:#9ca3af; cursor:not-allowed; }
            .cal-cell.available { background:#fef9c3; color:#854d0e; cursor:pointer; }
            .cal-cell.available:hover { background:#fef3c7; }
            .cal-cell.selected { background:#991b1b; color:#fff; }
            .cal-badge { position:absolute; right:6px; top:6px; background:rgba(255,255,255,0.95); border-radius:999px; padding:0 5px; font-size:0.65rem; color:#111827; border:1px solid #e5e7eb; min-width:18px; height:18px; display:inline-flex; align-items:center; justify-content:center; }
            .cal-badge.zero { color:#9ca3af; background:transparent; border:1px dashed #e5e7eb; }
        `;
        document.head.appendChild(style);
    })();

    async function ensureAvailabilityForView(){
        const key = `${viewYear}-${String(viewMonth+1).padStart(2,'0')}`;
        if(!availabilityByMonth[key]){
            const data = await fetchAvailability(viewYear, viewMonth);
            availabilityByMonth[key] = data || {};
        }
    }

    async function renderCalendar(){
        await ensureAvailabilityForView();
        const monthKey = `${viewYear}-${String(viewMonth+1).padStart(2,'0')}`;
        const avail = availabilityByMonth[monthKey] || {};

        monthTitle.textContent = new Intl.DateTimeFormat('en', { month: 'long', year: 'numeric' }).format(new Date(viewYear, viewMonth, 1));
        calendarGrid.innerHTML = '';

        const firstDay = new Date(viewYear, viewMonth, 1).getDay();
        const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

        for(let i=0; i<firstDay; i++){
            const blank = document.createElement('div');
            calendarGrid.appendChild(blank);
        }

        for(let day=1; day<=daysInMonth; day++){
            const dateObj = new Date(viewYear, viewMonth, day);
            dateObj.setHours(0,0,0,0);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'cal-cell';
            const daySpan = document.createElement('span');
            daySpan.className = 'day-num';
            daySpan.textContent = day;
            btn.appendChild(daySpan);

            const dateStr = formatDate(dateObj);
            const raw = avail[dateStr] || avail[dateStr.replace(/-0(\d)-/, '-$1-')] || avail[dateObj.getDate()] || avail[day] || null;
            let dayAvail = { count: 0, times: {} };
            if(raw !== null && raw !== undefined){
                if(typeof raw === 'number'){
                    dayAvail.count = raw;
                } else if(typeof raw === 'object'){
                    dayAvail.count = raw.count || raw.total || raw.booked || 0;
                    dayAvail.times = raw.times || raw.timeSlots || raw.slots || raw.t || {};
                }
            }
            const isWeekend = dateObj.getDay() === 0 || dateObj.getDay() === 6;
            const isPast = dateObj < today;
            const isSelected = selectedDateValue && sameDay(dateObj, selectedDateValue);

            if(isSelected){
                btn.classList.add('selected');
            } else if(isWeekend || isPast){
                btn.classList.add('disabled');
                btn.disabled = true;
            } else {
                btn.classList.add('available');
            }

            const badge = document.createElement('span');
            badge.className = 'cal-badge';
            const bookedCount = Number(dayAvail.count || 0);
            let availableForDay = Math.max(0, Number(MAX_AVAILABLE_PER_DAY) - bookedCount);
            if (availableForDay <= 0) {
                btn.classList.add('disabled');
                btn.disabled = true;
            }
            if (!isPast && !isWeekend) {
                badge.textContent = String(availableForDay);
                if (availableForDay === 0) badge.classList.add('zero');
                btn.appendChild(badge);
            }

            btn.addEventListener('click', function(){
                if(btn.disabled) return;
                selectedDateValue = dateObj;
                const formatted = dateStr;
                dateInput.value = formatted;
                renderCalendar();
                renderTimeSlots(formatted);
                showToast(`Selected ${formatted}`, 'success');
            });

            calendarGrid.appendChild(btn);
        }
    }

    async function renderTimeSlots(dateStr){
        timeSlotsEl.innerHTML = '';
        selectedTimeEl.textContent = '—';
        timeInput.value = '';
        if(!dateStr){
            timeHelp.style.display = '';
            return;
        }
        timeHelp.style.display = 'none';

        const monthKey = `${viewYear}-${String(viewMonth+1).padStart(2,'0')}`;
        const avail = availabilityByMonth[monthKey] || {};
        const raw = avail[dateStr] || avail[dateStr.replace(/-0(\d)-/, '-$1-')] || avail[dateStr.replace(/^(\d{4})-(\d)-(\d{1})$/, function(m,a,b,c){ return `${a}-${String(b).padStart(2,'0')}-${String(c).padStart(2,'0')}`; })] || null;
        let dayAvail = { count: 0, times: {} };
        if(raw !== null && raw !== undefined){
            if(typeof raw === 'number'){
                dayAvail.count = raw;
            } else if(typeof raw === 'object'){
                dayAvail.count = raw.count || raw.total || raw.booked || 0;
                dayAvail.times = raw.times || raw.timeSlots || raw.slots || raw.t || {};
            }
        }

        times.forEach(function(t){
            const slot = document.createElement('button');
            slot.type = 'button';
            slot.className = 'px-3 py-2 border rounded-lg text-sm flex items-center justify-between gap-2';
            slot.textContent = formatTimeLabel(t);
            const cnt = (dayAvail.times && (dayAvail.times[t] !== undefined)) ? Number(dayAvail.times[t]) : 0;
            const availableSlot = Math.max(0, Number(MAX_AVAILABLE_PER_SLOT) - cnt);
            const badge = document.createElement('span');
            badge.className = 'ml-2 text-xs text-slate-600';
            badge.textContent = String(availableSlot);
            slot.appendChild(badge);
            if (availableSlot <= 0) {
                slot.disabled = true;
                slot.classList.add('opacity-60','cursor-not-allowed');
            }

            slot.addEventListener('click', function(){
                if(slot.disabled) return;
                timeInput.value = t;
                selectedTimeEl.textContent = formatTimeLabel(t);
                Array.from(timeSlotsEl.children).forEach(c => c.classList.remove('bg-red-600','text-white'));
                slot.classList.add('bg-red-600','text-white');
                showToast(`Time set to ${formatTimeLabel(t)}`, 'success');
            });

            timeSlotsEl.appendChild(slot);
        });
    }

    prevMonthBtn.addEventListener('click', function(){
        const prev = new Date(viewYear, viewMonth - 1, 1);
        if(prev < today && prev.getFullYear() === today.getFullYear() && prev.getMonth() < today.getMonth()) return;
        viewYear = prev.getFullYear();
        viewMonth = prev.getMonth();
        renderCalendar();
    });
    nextMonthBtn.addEventListener('click', function(){
        const nxt = new Date(viewYear, viewMonth + 1, 1);
        viewYear = nxt.getFullYear();
        viewMonth = nxt.getMonth();
        renderCalendar();
    });

    (async function init(){
        await renderCalendar();
        if(selectedDateValue){
            const formatted = formatDate(selectedDateValue);
            dateInput.value = formatted;
            renderTimeSlots(formatted);
        }

        const urlParams = new URLSearchParams(window.location.search);
        const successParam = urlParams.get('success');
        const successMsgParam = urlParams.get('message');
        const storedToast = sessionStorage.getItem(TOAST_KEY);
        if(successParam || successMsgParam){
            const msg = successMsgParam || 'Appointment saved successfully.';
            showToast(msg, 'success');
            sessionStorage.removeItem(TOAST_KEY);
        } else if(storedToast){
            showToast(storedToast, 'success');
            sessionStorage.removeItem(TOAST_KEY);
        }
    })();

    if(form){
        form.addEventListener('submit', function(e){
            let hasError = false;
            if(!dateInput || !dateInput.value){
                hasError = true;
                showToast('Please select a date.', 'error');
            }
            if(!timeInput || !timeInput.value){
                hasError = true;
                showToast('Please select a time.', 'error');
            }
            if(hasError){
                e.preventDefault();
                e.stopPropagation();
                return;
            }
            sessionStorage.setItem(TOAST_KEY, 'Appointment submitted successfully.');
        });
    }
})();
</script>
