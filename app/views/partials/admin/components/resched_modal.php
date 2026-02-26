<div id="resched-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden flex items-center justify-center z-50 p-0">
    <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl relative animate-fadeIn">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 rounded-t-2xl">
            <button id="close-resched-modal" class="absolute top-4 right-4 text-white/80 hover:text-white text-3xl transition-colors">&times;</button>
            <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-calendar-alt"></i>
                Reschedule Appointment
            </h2>
            <p class="text-blue-100 text-sm mt-1">Select a new date and time for the appointment</p>
        </div>
        <form id="resched-form" class="p-6">
            <input type="hidden" id="resched-appt-id">
            <div class="grid grid-cols-3 gap-6 items-stretch">
                <div class="col-span-2 h-full flex flex-col">
                    <label class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-2">
                        <i class="fas fa-calendar-day text-blue-500"></i>
                        Select Date
                    </label>
                    <div class="relative">
                        <input type="hidden" id="resched-date" required>
                        <div id="resched-calendar" class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-white text-slate-800 h-full flex flex-col">
                            <div class="flex items-center justify-between mb-2">
                                <button type="button" id="resched-prev-month" class="text-slate-600 hover:text-slate-800">&lt;</button>
                                <div id="resched-month-title" class="font-semibold"></div>
                                <button type="button" id="resched-next-month" class="text-slate-600 hover:text-slate-800">&gt;</button>
                            </div>
                            <div class="grid grid-cols-7 gap-1 text-center text-xs text-slate-500">
                                <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                            </div>
                            <div id="resched-calendar-grid" class="grid grid-cols-7 gap-2 mt-2 flex-1"></div>
                        </div>
                    </div>
                </div>
                <div class="col-span-1 h-full flex flex-col">
                    <label class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-2">
                        <i class="fas fa-clock text-blue-500"></i>
                        Select Time
                    </label>
                    <input type="hidden" id="resched-time" required>
                    <div class="border border-slate-200 rounded-xl p-4 h-full flex flex-col justify-between">
                        <div class="mb-3">
                            <div class="text-xs text-slate-500">Selected</div>
                            <div id="resched-selected-time" class="text-lg font-semibold">—</div>
                        </div>
                        <div id="resched-time-slots" class="grid grid-cols-3 gap-2 overflow-auto p-1"></div>
                        <div class="mt-3 text-xs text-slate-500">
                            <strong class="text-slate-700">Note:</strong> The small number at the right of a day or time is the count of <em>approved</em> schedules for that specific day and time.
                        </div>
                    </div>
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