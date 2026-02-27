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
                    <div>
                        <label class="block text-sm font-medium">Contact Person *</label>
                        <input id="contact_person" name="contact_person" required class="w-full bg-gray-50 border rounded px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Contact Number *</label>
                        <input id="contact_number" name="contact_number" type="tel" inputmode="numeric" required pattern="^(09\d{9}|\+639\d{9})$" maxlength="13" placeholder="09123456789" class="w-full bg-gray-50 border rounded px-3 py-2"
                            oninput="this.value = this.value.replace(/[^0-9+]/g, '')" />
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Contact Address *</label>
                        <input id="contact_address" name="contact_address" required class="w-full bg-gray-50 border rounded px-3 py-2" />
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
<script src="/IDSystem/public/assets/js/booking.js"></script>