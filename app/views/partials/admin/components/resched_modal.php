<div id="resched-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl relative animate-fadeIn">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 rounded-t-2xl">
            <button id="close-resched-modal" class="absolute top-4 right-4 text-white/80 hover:text-white text-3xl transition-colors">&times;</button>
            <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-calendar-alt"></i>
                Reschedule Appointment
            </h2>
            <p class="text-blue-100 text-sm mt-1">Select a new date and time for the appointment</p>
        </div>
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