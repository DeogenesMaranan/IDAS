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

                <input type="hidden" id="appointment_date" name="appointment_date" />
            </div>
        </div>

        <div class="col-span-4">
            <aside class="card p-4 rounded-lg bg-white shadow-sm">
                <h3 class="font-semibold mb-2">Select Date</h3>
                <p class="text-sm text-gray-400 mb-4">Choose your preferred appointment date</p>
                <div id="calendar-placeholder" class="border rounded p-3"></div>

                <div class="mt-4 text-sm text-gray-600">
                    <p class="font-semibold">Legend:</p>
                    <ul class="mt-2">
                        <li><span class="inline-block w-3 h-3 bg-red-800 mr-2 align-middle"></span> Selected Date</li>
                        <li><span class="inline-block w-3 h-3 bg-yellow-400 mr-2 align-middle"></span> Available Dates</li>
                        <li><span class="inline-block w-3 h-3 bg-gray-200 mr-2 align-middle"></span> Unavailable (Weekends/Past)</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium">Select Time *</label>
                    <select id="time_slot" name="time_slot" disabled required class="w-full bg-gray-50 border rounded px-3 py-2 mt-2">
                        <option value="">Select time</option>
                        <option value="08:00-09:00">08:00 - 09:00</option>
                        <option value="09:00-10:00">09:00 - 10:00</option>
                        <option value="10:00-11:00">10:00 - 11:00</option>
                        <option value="11:00-12:00">11:00 - 12:00</option>
                        <option value="13:00-14:00">13:00 - 14:00</option>
                        <option value="14:00-15:00">14:00 - 15:00</option>
                        <option value="15:00-16:00">15:00 - 16:00</option>
                    </select>
                    <p id="time-help" class="text-sm text-gray-400 mt-2">Please select a date first to choose a time.</p>
                </div>
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
    const timeSelect = document.getElementById('time_slot');
    const timeHelp = document.getElementById('time-help');
    const dateInput = document.getElementById('appointment_date');
    const selectedDisplay = document.getElementById('selected-date-display');
    const calendar = document.getElementById('calendar-placeholder');
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

    function setSelectedDate(dateStr){
        if(!dateStr) return;
        dateInput.value = dateStr;
        if(selectedDisplay) selectedDisplay.textContent = dateStr;
        if(timeSelect){
            timeSelect.disabled = false;
        }
        if(timeHelp){
            timeHelp.style.display = 'none';
        }
        showToast(`Selected ${dateStr}`, 'success');
    }

    // Expose a global function so an external calendar can call it when a date is picked
    window.setSelectedAppointmentDate = setSelectedDate;

    // Fallback: if calendar-placeholder contains an <input type="date">, listen to changes
    const fallbackDateInput = calendar && calendar.querySelector('input[type="date"]');
    if(fallbackDateInput){
        fallbackDateInput.addEventListener('change', function(){ setSelectedDate(this.value); });
    }

    // Allow clicking elements inside the placeholder with data-date attribute (mock calendar)
    if(calendar){
        const style = document.createElement('style');
        style.textContent = `
            .cal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem; }
            .cal-btn { border:1px solid #d1d5db; background:#fff; border-radius:0.375rem; padding:0.25rem 0.5rem; font-size:0.875rem; cursor:pointer; }
            .cal-btn:disabled { opacity:0.5; cursor:not-allowed; }
            .cal-grid { display:grid; grid-template-columns:repeat(7, minmax(0, 1fr)); gap:0.35rem; font-size:0.85rem; }
            .cal-cell { border:1px solid #e5e7eb; border-radius:0.375rem; padding:0.5rem 0.35rem; text-align:center; background:#fff; }
            .cal-cell.disabled { background:#f3f4f6; color:#9ca3af; cursor:not-allowed; }
            .cal-cell.available { background:#fef9c3; color:#854d0e; cursor:pointer; }
            .cal-cell.available:hover { background:#fef3c7; }
            .cal-cell.selected { background:#991b1b; color:#fff; }
            .cal-weekday { text-align:center; font-weight:600; color:#6b7280; margin-bottom:0.3rem; }
        `;
        document.head.appendChild(style);

        const today = new Date();
        today.setHours(0,0,0,0);
        let viewYear = today.getFullYear();
        let viewMonth = today.getMonth(); // 0-indexed
        let selectedDateValue = dateInput && dateInput.value ? new Date(dateInput.value) : null;

        function formatDate(dateObj){
            const m = String(dateObj.getMonth() + 1).padStart(2, '0');
            const d = String(dateObj.getDate()).padStart(2, '0');
            return `${dateObj.getFullYear()}-${m}-${d}`;
        }

        function sameDay(a, b){
            return a && b && a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
        }

        function renderCalendar(){
            calendar.innerHTML = '';
            const header = document.createElement('div');
            header.className = 'cal-header';
            const monthLabel = document.createElement('div');
            const monthName = new Intl.DateTimeFormat('en', { month: 'long', year: 'numeric' }).format(new Date(viewYear, viewMonth, 1));
            monthLabel.textContent = monthName;

            const controls = document.createElement('div');
            const prevBtn = document.createElement('button');
            prevBtn.type = 'button'; // prevent form submit when navigating calendar
            prevBtn.className = 'cal-btn';
            prevBtn.textContent = 'Prev';
            const nextBtn = document.createElement('button');
            nextBtn.type = 'button'; // prevent form submit when navigating calendar
            nextBtn.className = 'cal-btn';
            nextBtn.textContent = 'Next';

            const isCurrentOrFutureMonth = (y, m) => y > today.getFullYear() || (y === today.getFullYear() && m >= today.getMonth());
            prevBtn.disabled = !isCurrentOrFutureMonth(viewYear, viewMonth);

            prevBtn.addEventListener('click', function(){
                const prevMonth = new Date(viewYear, viewMonth - 1, 1);
                if(isCurrentOrFutureMonth(prevMonth.getFullYear(), prevMonth.getMonth())){
                    viewYear = prevMonth.getFullYear();
                    viewMonth = prevMonth.getMonth();
                    renderCalendar();
                }
            });
            nextBtn.addEventListener('click', function(){
                const nextMonth = new Date(viewYear, viewMonth + 1, 1);
                viewYear = nextMonth.getFullYear();
                viewMonth = nextMonth.getMonth();
                renderCalendar();
            });

            controls.appendChild(prevBtn);
            controls.appendChild(nextBtn);
            header.appendChild(monthLabel);
            header.appendChild(controls);
            calendar.appendChild(header);

            const weekdaysRow = document.createElement('div');
            weekdaysRow.className = 'cal-grid';
            const weekdays = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
            weekdays.forEach(function(day){
                const el = document.createElement('div');
                el.className = 'cal-weekday';
                el.textContent = day;
                weekdaysRow.appendChild(el);
            });
            calendar.appendChild(weekdaysRow);

            const grid = document.createElement('div');
            grid.className = 'cal-grid';

            const firstDay = new Date(viewYear, viewMonth, 1).getDay();
            const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

            for(let i=0; i<firstDay; i++){
                const blank = document.createElement('div');
                grid.appendChild(blank);
            }

            for(let day=1; day<=daysInMonth; day++){
                const dateObj = new Date(viewYear, viewMonth, day);
                dateObj.setHours(0,0,0,0);
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'cal-cell';
                btn.textContent = day;

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

                btn.addEventListener('click', function(){
                    if(btn.disabled) return;
                    selectedDateValue = dateObj;
                    const formatted = formatDate(dateObj);
                    setSelectedDate(formatted);
                    renderCalendar();
                });

                grid.appendChild(btn);
            }

            calendar.appendChild(grid);
        }

        renderCalendar();
    }

    if(timeSelect && timeHelp){
        if(!timeSelect.disabled) timeHelp.style.display = 'none';
        else timeHelp.style.display = '';
    }

    // On page load, show success toast if backend redirected with success params
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

    if(timeSelect){
        timeSelect.addEventListener('change', function(){
            if(this.value){
                showToast(`Time set to ${this.value}`, 'success');
            }
        });
    }

    if(form){
        form.addEventListener('submit', function(e){
            let hasError = false;
            if(!dateInput || !dateInput.value){
                hasError = true;
                showToast('Please select a date.', 'error');
            }
            if(!timeSelect || !timeSelect.value){
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
