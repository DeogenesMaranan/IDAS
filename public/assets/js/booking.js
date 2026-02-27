(function(){
    const form = document.getElementById('booking-form');
    const calendarGrid = document.getElementById('booking-calendar-grid');
    const monthTitle = document.getElementById('booking-month-title');
    const prevMonthBtn = document.getElementById('booking-prev-month');
    const nextMonthBtn = document.getElementById('booking-next-month');
    const dateInput = document.getElementById('appointment_date');
    const timeInput = document.getElementById('appointment_time');
    const timeSlotsEl = document.getElementById('booking-time-slots');
    const selectedTimeEl = document.getElementById('booking-selected-time');
    const timeHelp = document.getElementById('booking-time-help');
    const contactPersonInput = document.getElementById('contact_person');
    const contactAddressInput = document.getElementById('contact_address');
    const contactNumberInput = document.getElementById('contact_number');

    const TOAST_KEY = 'booking_success_toast';
    const TIMES = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00'];
    let MAX_AVAILABLE_PER_DAY = 100;
    let MAX_AVAILABLE_PER_SLOT = 100;
    const availabilityByMonth = new Map();

    const today = new Date(); today.setHours(0,0,0,0);
    let viewYear = today.getFullYear();
    let viewMonth = today.getMonth();
    let selectedDateValue = dateInput && dateInput.value ? new Date(dateInput.value) : null;

    function isValidPhone(val){
        if(!val) return false;
        const cleaned = String(val).replace(/\D/g,'');
        return /^09\d{9}$/.test(cleaned);
    }

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

    function formatTimeLabel(t24){
        if(!t24) return '';
        const [hh, mm='00'] = t24.split(':');
        let hour = parseInt(hh,10);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12 || 12;
        return `${String(hour).padStart(2,'0')}:${mm} ${ampm}`;
    }

    function formatDate(d){
        const m = String(d.getMonth()+1).padStart(2,'0');
        const day = String(d.getDate()).padStart(2,'0');
        return `${d.getFullYear()}-${m}-${day}`;
    }

    function sameDay(a,b){
        return a && b && a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate();
    }

    (function addCalendarStyles(){
        const s = document.createElement('style');
        s.textContent = `
            .cal-cell { border:1px solid #e5e7eb; border-radius:0.375rem; padding:0.8rem 0.35rem 0.4rem 0.35rem; text-align:center; background:#fff; position:relative; min-height:48px; display:flex; align-items:flex-start; justify-content:center; }
            .cal-cell .day-num { display:block; margin-top:6px; font-weight:600; }
            .cal-cell.disabled { background:#f3f4f6; color:#9ca3af; cursor:not-allowed; }
            .cal-cell.available { background:#fef9c3; color:#854d0e; cursor:pointer; }
            .cal-cell.available:hover { background:#fef3c7; }
            .cal-cell.selected { background:#991b1b; color:#fff; }
            .cal-badge { position:absolute; right:6px; top:6px; background:rgba(255,255,255,0.95); border-radius:999px; padding:0 5px; font-size:0.65rem; color:#111827; border:1px solid #e5e7eb; min-width:18px; height:18px; display:inline-flex; align-items:center; justify-content:center; }
            .cal-badge.zero { color:#9ca3af; background:transparent; border:1px dashed #e5e7eb; }
        `;
        document.head.appendChild(s);
    })();

    function normalizeAvailability(data){
        const out = {};
        if(!data) return out;

        if(Array.isArray(data)){
            data.forEach(item => {
                const date = item.date || item.day || item.d || item.dayString;
                if(!date) return;
                const dateStr = String(date);
                const cnt = Number(item.count || item.total || item.booked || item.c || 0);
                const times = {};
                if(item.times && typeof item.times === 'object' && !Array.isArray(item.times)) Object.assign(times, item.times);
                else if(Array.isArray(item.times)) item.times.forEach(ts => { if(ts.time) times[ts.time] = Number(ts.count || ts.c || 0); });
                out[dateStr] = { count: cnt, times };
            });
            return out;
        }

        if(typeof data === 'object'){
            const keys = Object.keys(data);
            if(keys.length === 1 && keys[0].indexOf('-')>0 && typeof data[keys[0]] === 'object') data = data[keys[0]];

            for(const k in data){
                if(!Object.prototype.hasOwnProperty.call(data,k)) continue;
                const val = data[k];
                const dateStr = String(k);
                if(typeof val === 'number') out[dateStr] = { count: Number(val), times: {} };
                else if(typeof val === 'object'){
                    const cnt = Number(val.count || val.total || val.booked || 0);
                    const times = {};
                    if(val.times && typeof val.times === 'object') Object.assign(times, val.times);
                    else if(Array.isArray(val.timeSlots)) val.timeSlots.forEach(ts => { if(ts.time) times[ts.time] = Number(ts.count || ts.c || 0); });
                    else if(Array.isArray(val.slots)) val.slots.forEach(ts => { if(ts.time) times[ts.time] = Number(ts.count || ts.c || 0); });
                    out[dateStr] = { count: cnt, times };
                }
            }
            return out;
        }

        return out;
    }

    async function fetchAvailability(year, monthIndex){
        try{
            const month = String(monthIndex+1).padStart(2,'0');
            const url = `/IDSystem/appointments/availability?year=${year}&month=${month}`;
            const res = await fetch(url, { credentials: 'same-origin' });
            if(!res.ok) return {};
            const data = await res.json();
            if(data && data._meta){
                if(data._meta.max_per_day !== undefined) MAX_AVAILABLE_PER_DAY = Number(data._meta.max_per_day);
                if(data._meta.max_per_slot !== undefined) MAX_AVAILABLE_PER_SLOT = Number(data._meta.max_per_slot);
                delete data._meta;
            }
            return normalizeAvailability(data);
        }catch(e){
            console.debug('fetchAvailability error', e);
            return {};
        }
    }

    async function ensureAvailabilityForView(){
        const key = `${viewYear}-${String(viewMonth+1).padStart(2,'0')}`;
        if(!availabilityByMonth.has(key)){
            const data = await fetchAvailability(viewYear, viewMonth);
            availabilityByMonth.set(key, data || {});
        }
    }

    async function renderCalendar(){
        await ensureAvailabilityForView();
        const monthKey = `${viewYear}-${String(viewMonth+1).padStart(2,'0')}`;
        const avail = availabilityByMonth.get(monthKey) || {};

        monthTitle.textContent = new Intl.DateTimeFormat('en', { month: 'long', year: 'numeric' }).format(new Date(viewYear, viewMonth, 1));
        calendarGrid.innerHTML = '';

        const firstDay = new Date(viewYear, viewMonth, 1).getDay();
        const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

        for(let i=0;i<firstDay;i++) calendarGrid.appendChild(document.createElement('div'));

        for(let day=1; day<=daysInMonth; day++){
            const dateObj = new Date(viewYear, viewMonth, day);
            dateObj.setHours(0,0,0,0);
            const btn = document.createElement('button'); btn.type='button'; btn.className='cal-cell';
            const daySpan = document.createElement('span'); daySpan.className='day-num'; daySpan.textContent = day; btn.appendChild(daySpan);

            const dateStr = formatDate(dateObj);
            const raw = avail[dateStr] || avail[dateObj.getDate()] || avail[String(day)];
            const dayAvail = raw ? (typeof raw === 'number' ? { count: Number(raw), times: {} } : { count: Number(raw.count||0), times: raw.times||{} }) : { count:0, times:{} };

            const isWeekend = dateObj.getDay()===0 || dateObj.getDay()===6;
            const isPast = dateObj < today;
            const isSelected = selectedDateValue && sameDay(dateObj, selectedDateValue);

            if(isSelected) btn.classList.add('selected');
            else if(isWeekend || isPast) { btn.classList.add('disabled'); btn.disabled = true; }
            else btn.classList.add('available');

            const bookedCount = Number(dayAvail.count || 0);
            let availableForDay = Math.max(0, Number(MAX_AVAILABLE_PER_DAY) - bookedCount);
            if(availableForDay <= 0){ btn.classList.add('disabled'); btn.disabled = true; }

            if(!isPast && !isWeekend){
                const badge = document.createElement('span'); badge.className='cal-badge'; badge.textContent = String(availableForDay);
                if(availableForDay===0) badge.classList.add('zero');
                btn.appendChild(badge);
            }

            btn.addEventListener('click', ()=>{
                if(btn.disabled) return;
                selectedDateValue = dateObj;
                dateInput.value = dateStr;
                renderCalendar();
                renderTimeSlots(dateStr);
                showToast(`Selected ${dateStr}`, 'success');
            });

            calendarGrid.appendChild(btn);
        }
    }

    async function renderTimeSlots(dateStr){
        timeSlotsEl.innerHTML = '';
        selectedTimeEl.textContent = '—';
        timeInput.value = '';
        if(!dateStr){ timeHelp.style.display=''; return; }
        timeHelp.style.display='none';

        const monthKey = `${viewYear}-${String(viewMonth+1).padStart(2,'0')}`;
        const avail = availabilityByMonth.get(monthKey) || {};
        const raw = avail[dateStr] || avail[dateStr.replace(/-0(\d)-/,'-$1-')];
        const dayAvail = raw ? (typeof raw === 'number' ? { count:Number(raw), times:{} } : { count:Number(raw.count||0), times: raw.times||{} }) : { count:0, times:{} };

        TIMES.forEach(t => {
            const slot = document.createElement('button'); slot.type='button'; slot.className='px-3 py-2 border rounded-lg text-sm flex items-center justify-between gap-2';
            slot.textContent = formatTimeLabel(t);
            const cnt = dayAvail.times && (dayAvail.times[t] !== undefined) ? Number(dayAvail.times[t]) : 0;
            const availableSlot = Math.max(0, Number(MAX_AVAILABLE_PER_SLOT) - cnt);
            const badge = document.createElement('span'); badge.className='ml-2 text-xs text-slate-600'; badge.textContent = String(availableSlot); slot.appendChild(badge);
            if(availableSlot <= 0){ slot.disabled = true; slot.classList.add('opacity-60','cursor-not-allowed'); }

            slot.addEventListener('click', ()=>{
                if(slot.disabled) return;
                timeInput.value = t;
                selectedTimeEl.textContent = formatTimeLabel(t);
                Array.from(timeSlotsEl.children).forEach(c=> c.classList.remove('bg-red-600','text-white'));
                slot.classList.add('bg-red-600','text-white');
                showToast(`Time set to ${formatTimeLabel(t)}`, 'success');
            });

            timeSlotsEl.appendChild(slot);
        });
    }

    prevMonthBtn.addEventListener('click', ()=>{
        const prev = new Date(viewYear, viewMonth-1, 1);
        if(prev < today && prev.getFullYear() === today.getFullYear() && prev.getMonth() < today.getMonth()) return;
        viewYear = prev.getFullYear(); viewMonth = prev.getMonth(); renderCalendar();
    });
    nextMonthBtn.addEventListener('click', ()=>{ const nxt = new Date(viewYear, viewMonth+1, 1); viewYear = nxt.getFullYear(); viewMonth = nxt.getMonth(); renderCalendar(); });

    (async function init(){
        await renderCalendar();
        if(selectedDateValue){ const f = formatDate(selectedDateValue); dateInput.value = f; renderTimeSlots(f); }

        const urlParams = new URLSearchParams(window.location.search);
        const successParam = urlParams.get('success');
        const successMsgParam = urlParams.get('message');
        const storedToast = sessionStorage.getItem(TOAST_KEY);
        if(successParam || successMsgParam){ showToast(successMsgParam || 'Appointment saved successfully.', 'success'); sessionStorage.removeItem(TOAST_KEY); }
        else if(storedToast){ showToast(storedToast, 'success'); sessionStorage.removeItem(TOAST_KEY); }
    })();

    if(form){
        form.addEventListener('submit', function(e){
            let hasError = false;
            if(!dateInput || !dateInput.value){ hasError = true; showToast('Please select a date.', 'error'); }
            if(!timeInput || !timeInput.value){ hasError = true; showToast('Please select a time.', 'error'); }
            if(!contactPersonInput || !contactPersonInput.value.trim()){ hasError = true; showToast('Contact person is required.', 'error'); }
            if(!contactNumberInput || !contactNumberInput.value.trim()){ hasError = true; showToast('Contact number is required.', 'error'); }
            else if(!isValidPhone(contactNumberInput.value)){ hasError = true; showToast('Contact number format is invalid. Use 09xxxxxxxxx or +639xxxxxxxxx', 'error'); }
            if(!contactAddressInput || !contactAddressInput.value.trim()){ hasError = true; showToast('Contact address is required.', 'error'); }
            if(hasError){ e.preventDefault(); e.stopPropagation(); return; }
            sessionStorage.setItem(TOAST_KEY, 'Appointment submitted successfully.');
        });
    }

})();
