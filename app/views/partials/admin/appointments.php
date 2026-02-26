<div class="space-y-6">
    <div class="relative overflow-hidden">
        <div class="relative px-6 py-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">Appointment Management</h1>
            <p class="text-slate-600 text-lg mt-1 flex items-center gap-2">
                View and manage all ID appointment requests
            </p>
        </div>
    </div>

    <div id="summary-bar" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
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

</div>

<script type="module" src="/IDSystem/public/assets/js/appointments.js"></script>