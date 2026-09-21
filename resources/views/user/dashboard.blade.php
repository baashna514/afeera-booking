@extends('layouts.app', ['title' => 'Ticket Counter Terminal - Online Ticketing Software'])

@section('content')
<div class="space-y-6" x-data="ticketCounter()">

    <!-- Top Counter Banner (Clean White with Dark Slate Text) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 text-slate-900 shadow-sm border border-slate-200 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-[#da1705] border border-red-200 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-desktop"></i> Ticket Counter Terminal
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 flex items-center gap-3">
                    <span>Counter Staff: {{ auth()->user()->name }}</span>
                </h1>
                <p class="text-xs font-semibold text-slate-600 mt-1 max-w-xl">
                    Select route, date, departure schedule, and pick seats to issue printed tickets to passengers.
                </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="bg-slate-900 px-4 py-2.5 rounded-2xl text-right text-white">
                    <span class="text-[10px] font-bold text-slate-300 uppercase block">Today's Sales</span>
                    <span class="text-xl font-black text-white">Rs. {{ number_format($todayRevenue ?? 0, 0) }}</span>
                </div>
                <div class="bg-slate-900 px-4 py-2.5 rounded-2xl text-right text-white">
                    <span class="text-[10px] font-bold text-slate-300 uppercase block">Tickets Issued</span>
                    <span class="text-xl font-black text-[#da1705]">{{ $todayBookings ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Terminal Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Step 1 & 2: Search Route & Schedule -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-5">
                <h3 class="font-black text-slate-900 text-lg flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-red-50 text-[#da1705] flex items-center justify-center text-xs font-bold">1</span>
                    <span>Select Journey</span>
                </h3>

                <!-- Select Route -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Select Bus Route *</label>
                    <select x-model="selectedRouteId" @change="onRouteOrDateChange()" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] focus:border-[#da1705] text-sm text-slate-800 bg-white">
                        <option value="">-- Choose Route --</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}">
                                {{ $route->name }} ({{ $route->originCity->name ?? '' }} → {{ $route->destinationCity->name ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Date -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Travel Date *</label>
                    <input type="date" x-model="selectedDate" min="{{ date('Y-m-d') }}" @change="onRouteOrDateChange()"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800">
                </div>

                <!-- Available Departure Schedules -->
                <div x-show="selectedRouteId && selectedDate" class="space-y-3 pt-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Available Departure Schedules</label>

                    <template x-if="loadingSchedules">
                        <div class="py-4 text-center text-xs text-slate-400">
                            <i class="fa-solid fa-spinner animate-spin mr-1 text-[#da1705]"></i> Loading departure times...
                        </div>
                    </template>

                    <template x-if="!loadingSchedules && schedules.length === 0">
                        <div class="p-4 bg-amber-50 rounded-xl text-xs text-amber-800 border border-amber-200">
                            No active departure schedules found for this route.
                        </div>
                    </template>

                    <div class="space-y-2">
                        <template x-for="sch in schedules" :key="sch.id">
                            <div @click="selectSchedule(sch)"
                                :class="selectedSchedule && selectedSchedule.id === sch.id ? 'border-[#da1705] bg-red-50/70 shadow-md ring-2 ring-red-500/20' : 'border-slate-200 hover:border-slate-300 bg-white'"
                                class="p-3.5 rounded-2xl border cursor-pointer transition flex items-center justify-between">
                                <div>
                                    <div class="font-mono font-black text-slate-900 text-base flex items-center gap-1.5">
                                        <i class="fa-solid fa-clock text-[#da1705] text-xs"></i>
                                        <span x-text="formatTime(sch.departure_time)"></span>
                                    </div>
                                    <div class="text-[11px] font-semibold text-slate-500 mt-0.5" x-text="sch.vehicle_type"></div>
                                    <template x-if="sch.assigned_vehicle">
                                        <div class="text-[10px] font-bold text-[#da1705] mt-0.5" x-text="sch.assigned_vehicle"></div>
                                    </template>
                                </div>
                                <div class="text-right">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold"
                                        :class="sch.available_seats > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'">
                                        <span x-text="sch.available_seats"></span> seats left
                                    </span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Interactive Seat Layout Map & Passenger Form -->
        <div class="lg:col-span-2 space-y-6">

            <div x-show="!selectedSchedule" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-[#da1705] flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-solid fa-bus-simple"></i>
                </div>
                <h4 class="text-base font-bold text-slate-800">Select Journey & Departure Schedule</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Choose a bus route, travel date, and departure time from the left panel to load the interactive bus seat map.
                </p>
            </div>

            <div x-show="selectedSchedule" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">

                <!-- Header info -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
                    <div>
                        <h3 class="font-black text-slate-900 text-lg flex items-center gap-2">
                            <i class="fa-solid fa-chair text-[#da1705]"></i>
                            <span>Interactive Bus Seat Map</span>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-red-50 text-[#da1705]" x-text="selectedSchedule ? selectedSchedule.vehicle_type : ''"></span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Click an available (green) seat to lock and book. Back row seats are arranged in 1 continuous row.</p>
                    </div>
                    <!-- Seat legend -->
                    <div class="flex items-center gap-3 text-xs font-semibold flex-wrap">
                        <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-emerald-500"></span> Available</span>
                        <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-[#da1705]"></span> Selected / Locked</span>
                        <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-amber-500"></span> Held at other counter</span>
                        <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-pink-500"></span> Female</span>
                        <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-slate-700"></span> Male</span>
                    </div>
                </div>

                <!-- Hold Error / Conflict Message -->
                <template x-if="holdErrorMessage">
                    <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
                            <span x-text="holdErrorMessage"></span>
                        </div>
                        <button type="button" @click="holdErrorMessage = null" class="text-rose-400 hover:text-rose-600 transition">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                </template>

                <!-- Bus Layout Container -->
                <div class="p-6 bg-slate-100 rounded-3xl border border-slate-200 max-w-md mx-auto relative space-y-4">
                    <!-- Driver Cabin Indicator -->
                    <div class="flex items-center justify-between pb-3 border-b-2 border-dashed border-slate-300">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400"><i class="fa-solid fa-door-open mr-1"></i> Entrance</span>
                        <span class="px-3 py-1 bg-slate-200 text-slate-700 rounded-lg text-xs font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-dharmachakra text-[#da1705]"></i> Driver
                        </span>
                    </div>

                    <!-- Main Seating Layout (2 + 2 layout with aisle) -->
                    <div class="grid grid-cols-5 gap-3">
                        <template x-for="seat in mainSeats" :key="seat.number">
                            <div class="contents">
                                <!-- Aisle gap after 2 seats -->
                                <template x-if="(seat.number - 1) % 4 === 2">
                                    <div class="col-span-1"></div>
                                </template>

                                <button type="button"
                                    @click="selectSeat(seat)"
                                    :disabled="seat.is_booked || seat.is_held_by_other"
                                    :title="seat.is_held_by_other ? 'Currently held by another counter terminal' : (seat.is_booked ? 'Booked' : 'Seat #' + seat.number)"
                                    :class="{
                                        'bg-[#da1705] text-white font-black ring-4 ring-red-300 scale-105 shadow-md': selectedSeatNumber === seat.number,
                                        'bg-amber-500 text-white cursor-not-allowed opacity-90 shadow-sm': seat.is_held_by_other && selectedSeatNumber !== seat.number,
                                        'bg-pink-500 text-white cursor-not-allowed': seat.is_booked && seat.gender === 'female',
                                        'bg-slate-700 text-white cursor-not-allowed': seat.is_booked && seat.gender === 'male',
                                        'bg-slate-400 text-slate-100 cursor-not-allowed': seat.is_booked && !seat.gender,
                                        'bg-emerald-500 text-white hover:bg-emerald-600 shadow-sm cursor-pointer': !seat.is_booked && !seat.is_held_by_other && selectedSeatNumber !== seat.number
                                    }"
                                    class="h-11 rounded-xl flex items-center justify-center font-bold text-xs transition relative">
                                    <span x-text="seat.number"></span>
                                    <template x-if="seat.is_held_by_other">
                                        <i class="fa-solid fa-lock text-[8px] absolute top-1 right-1 text-amber-100" title="Held by other counter"></i>
                                    </template>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Continuous Back Row (Last 5 seats straight across without aisle gap) -->
                    <template x-if="backSeats.length > 0">
                        <div class="pt-3 border-t-2 border-dashed border-slate-300">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2 text-center">Continuous Back Row</span>
                            <div class="grid grid-cols-5 gap-2">
                                <template x-for="seat in backSeats" :key="seat.number">
                                    <button type="button"
                                        @click="selectSeat(seat)"
                                        :disabled="seat.is_booked || seat.is_held_by_other"
                                        :title="seat.is_held_by_other ? 'Currently held by another counter terminal' : (seat.is_booked ? 'Booked' : 'Seat #' + seat.number)"
                                        :class="{
                                            'bg-[#da1705] text-white font-black ring-4 ring-red-300 scale-105 shadow-md': selectedSeatNumber === seat.number,
                                            'bg-amber-500 text-white cursor-not-allowed opacity-90 shadow-sm': seat.is_held_by_other && selectedSeatNumber !== seat.number,
                                            'bg-pink-500 text-white cursor-not-allowed': seat.is_booked && seat.gender === 'female',
                                            'bg-slate-700 text-white cursor-not-allowed': seat.is_booked && seat.gender === 'male',
                                            'bg-slate-400 text-slate-100 cursor-not-allowed': seat.is_booked && !seat.gender,
                                            'bg-emerald-500 text-white hover:bg-emerald-600 shadow-sm cursor-pointer': !seat.is_booked && !seat.is_held_by_other && selectedSeatNumber !== seat.number
                                        }"
                                        class="h-11 rounded-xl flex items-center justify-center font-bold text-xs transition relative">
                                        <span x-text="seat.number"></span>
                                        <template x-if="seat.is_held_by_other">
                                            <i class="fa-solid fa-lock text-[8px] absolute top-1 right-1 text-amber-100" title="Held by other counter"></i>
                                        </template>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Ticket Confirmation Form -->
                <form action="{{ route('user.booking.store') }}" method="POST" class="pt-4 border-t border-slate-100 space-y-5">
                    @csrf
                    <input type="hidden" name="schedule_id" :value="selectedSchedule ? selectedSchedule.id : ''">
                    <input type="hidden" name="booking_date" :value="selectedDate">
                    <input type="hidden" name="seat_number" :value="selectedSeatNumber">

                    <div class="bg-red-50/70 p-4 rounded-2xl border border-red-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Selected Seat #</span>
                            <span class="text-2xl font-black text-[#da1705]" x-text="selectedSeatNumber ? 'Seat ' + selectedSeatNumber : 'None selected'"></span>
                            <template x-if="holdTimeRemaining > 0 && selectedSeatNumber">
                                <span class="text-[11px] font-bold text-amber-800 bg-amber-100 border border-amber-300 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1.5 mt-1.5 shadow-2xs">
                                    <i class="fa-solid fa-stopwatch animate-pulse text-[#da1705]"></i> Locked for: <span class="font-mono" x-text="formattedHoldTimer"></span>
                                </span>
                            </template>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Calculated Fare</span>
                            <span class="text-2xl font-black text-emerald-600" x-text="calculatedFare ? 'Rs. ' + calculatedFare : 'Rs. 1,500'"></span>
                        </div>
                    </div>
                    <input type="hidden" name="fare_amount" :value="calculatedFare > 0 ? calculatedFare : 1500">

                    <!-- Adjacent Gender Policy Notice -->
                    <template x-if="adjacentGenderNotice">
                        <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl text-xs font-semibold flex items-center gap-2">
                            <i class="fa-solid fa-person-half-dress text-amber-600 text-base"></i>
                            <span x-text="adjacentGenderNotice"></span>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Boarding City (From) *</label>
                            <select name="from_city_id" x-model="fromCityId" @change="fetchFare()" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800 bg-white">
                                <option value="">-- Select Boarding City --</option>
                                @foreach(\App\Models\City::where('status', 'active')->orderBy('name')->get() as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Destination City (To) *</label>
                            <select name="to_city_id" x-model="toCityId" @change="fetchFare()" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800 bg-white">
                                <option value="">-- Select Destination City --</option>
                                @foreach(\App\Models\City::where('status', 'active')->orderBy('name')->get() as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Passenger Full Name *</label>
                            <input type="text" name="passenger_name" x-model="passengerName" placeholder="Enter passenger name" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Passenger Mobile # *</label>
                            <input type="text" name="phone_number" x-model="phoneNumber" placeholder="0300-1234567" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Passenger CNIC / ID (Optional)</label>
                            <input type="text" name="cnic" x-model="cnic" placeholder="32102-xxxxxxx-x"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Passenger Gender *</label>
                            <select name="gender" x-model="gender" :disabled="genderLocked" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800 bg-white disabled:bg-slate-100 disabled:text-slate-500">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <template x-if="genderLocked">
                                <input type="hidden" name="gender" :value="gender">
                            </template>
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-3">
                        <button type="button" @click="showExpenseModal = true"
                            class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-2xl transition flex items-center gap-1.5">
                            <i class="fa-solid fa-calculator text-[#da1705]"></i> Record Expenses & Trip Voucher
                        </button>

                        <button type="submit" :disabled="!selectedSeatNumber"
                            class="px-8 py-3.5 bg-[#da1705] hover:bg-[#b91204] disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-sm rounded-2xl shadow-lg shadow-red-900/30 transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-print"></i> Issue & Print Ticket
                        </button>
                    </div>
                </form>

                <!-- Terminal Expenses & Trip Dispatch Voucher Modal -->
                <div x-show="showExpenseModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4" style="display: none;">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 border border-slate-200 shadow-2xl">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="font-black text-slate-900 text-base flex items-center gap-2">
                                    <i class="fa-solid fa-receipt text-[#da1705]"></i>
                                    <span>Terminal Expenses & Dispatch Voucher</span>
                                </h3>
                                <p class="text-xs text-slate-500">Record terminal expenses to generate official trip dispatch voucher for driver/guard.</p>
                            </div>
                            <button type="button" @click="showExpenseModal = false" class="text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <form action="{{ route('user.terminal.voucher') }}" method="POST" target="_blank" class="space-y-4">
                            @csrf
                            <input type="hidden" name="schedule_id" :value="selectedSchedule ? selectedSchedule.id : ''">
                            <input type="hidden" name="booking_date" :value="selectedDate">
                            <!-- Hidden ticket fields if generating voucher directly with seat selection -->
                            <input type="hidden" name="seat_number" :value="selectedSeatNumber">
                            <input type="hidden" name="from_city_id" :value="fromCityId">
                            <input type="hidden" name="to_city_id" :value="toCityId">
                            <input type="hidden" name="passenger_name" :value="passengerName">
                            <input type="hidden" name="phone_number" :value="phoneNumber">
                            <input type="hidden" name="cnic" :value="cnic">
                            <input type="hidden" name="gender" :value="gender">
                            <input type="hidden" name="fare_amount" :value="calculatedFare > 0 ? calculatedFare : 1500">

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Terminal Station Name</label>
                                <input type="text" name="terminal_city" placeholder="e.g. Layyah Terminal / ShorKot Counter"
                                    class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705]">
                            </div>

                            <!-- Dynamic Expenses Adder -->
                            <div class="space-y-3 pt-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Terminal Expenses Breakdown</label>
                                    <button type="button" @click="addExpenseRow()" class="text-xs font-bold text-[#da1705] hover:underline flex items-center gap-1">
                                        <i class="fa-solid fa-plus-circle"></i> Add Expense Row
                                    </button>
                                </div>

                                <div class="space-y-2.5 max-h-48 overflow-y-auto pr-1">
                                    <template x-for="(exp, index) in expenseRows" :key="index">
                                        <div class="flex items-center gap-2 bg-slate-50 p-2.5 rounded-2xl border border-slate-200">
                                            <div class="flex-1">
                                                <select :name="'expenses[' + index + '][expense_type_id]'" x-model="exp.expense_type_id" class="w-full px-3 py-1.5 text-xs font-medium rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-[#da1705]">
                                                    <option value="">-- Select Expense Title --</option>
                                                    @foreach($expenseTypes ?? [] as $expType)
                                                        <option value="{{ $expType->id }}">{{ $expType->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="w-32">
                                                <input type="number" step="10" min="0" :name="'expenses[' + index + '][amount]'" x-model="exp.amount" placeholder="Amount (Rs.)"
                                                    class="w-full px-3 py-1.5 text-xs font-medium rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705]">
                                            </div>
                                            <button type="button" @click="removeExpenseRow(index)" class="w-8 h-8 flex items-center justify-center text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Remove Row">
                                                <i class="fa-solid fa-xmark text-sm"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Optional Staff Expenses (Final Voucher) -->
                            <div class="pt-3 border-t border-slate-100 space-y-3">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Trip Staff Expenses (Optional - Driver/Guard/Hostess)</label>
                                <div class="grid grid-cols-3 gap-2 text-xs">
                                    <div>
                                        <span class="block font-medium text-slate-600 mb-1">Driver Fee</span>
                                        <input type="number" step="50" min="0" name="driver_allowance" placeholder="Rs. 0" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs">
                                    </div>
                                    <div>
                                        <span class="block font-medium text-slate-600 mb-1">Guard Fee</span>
                                        <input type="number" step="50" min="0" name="guard_allowance" placeholder="Rs. 0" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs">
                                    </div>
                                    <div>
                                        <span class="block font-medium text-slate-600 mb-1">Hostess Fee</span>
                                        <input type="number" step="50" min="0" name="hostess_allowance" placeholder="Rs. 0" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs">
                                    </div>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                                <button type="button" @click="showExpenseModal = false" class="px-4 py-2 text-xs font-bold text-slate-600">Cancel</button>
                                <button type="submit" @click="showExpenseModal = false"
                                    class="px-6 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-2">
                                    <i class="fa-solid fa-file-contract"></i> Save & Generate Voucher
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

<script>
function ticketCounter() {
    return {
        selectedRouteId: '',
        selectedDate: '{{ date("Y-m-d") }}',
        schedules: [],
        loadingSchedules: false,
        selectedSchedule: null,
        seatMap: [],
        mainSeats: [],
        backSeats: [],
        selectedSeatNumber: null,
        fromCityId: '',
        toCityId: '',
        passengerName: '',
        phoneNumber: '',
        cnic: '',
        gender: 'male',
        genderLocked: false,
        adjacentGenderNotice: null,
        calculatedFare: 0,
        showExpenseModal: false,
        holdErrorMessage: null,
        holdTimerInterval: null,
        holdTimeRemaining: 0,
        formattedHoldTimer: '05:00',
        expenseRows: [
            { expense_type_id: '', amount: '' }
        ],

        init() {
            // Background polling every 15 seconds to sync counter seat holds
            setInterval(() => {
                if (this.selectedSchedule) {
                    this.reloadSeatMap();
                }
            }, 15000);
        },

        addExpenseRow() {
            this.expenseRows.push({ expense_type_id: '', amount: '' });
        },

        removeExpenseRow(index) {
            if (this.expenseRows.length > 1) {
                this.expenseRows.splice(index, 1);
            } else {
                this.expenseRows = [{ expense_type_id: '', amount: '' }];
            }
        },

        startHoldTimer(seconds) {
            this.stopHoldTimer();
            this.holdTimeRemaining = seconds;
            this.updateFormattedTimer();
            this.holdTimerInterval = setInterval(() => {
                this.holdTimeRemaining--;
                if (this.holdTimeRemaining <= 0) {
                    this.stopHoldTimer();
                    this.selectedSeatNumber = null;
                    this.holdErrorMessage = "Temporary seat lock has expired (5-minute timeout). Please select a seat again.";
                    this.reloadSeatMap();
                } else {
                    this.updateFormattedTimer();
                }
            }, 1000);
        },

        stopHoldTimer() {
            if (this.holdTimerInterval) {
                clearInterval(this.holdTimerInterval);
                this.holdTimerInterval = null;
            }
            this.holdTimeRemaining = 0;
            this.formattedHoldTimer = '05:00';
        },

        updateFormattedTimer() {
            const mins = Math.floor(this.holdTimeRemaining / 60);
            const secs = this.holdTimeRemaining % 60;
            this.formattedHoldTimer = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        },

        onRouteOrDateChange() {
            this.selectedSchedule = null;
            this.seatMap = [];
            this.mainSeats = [];
            this.backSeats = [];
            this.selectedSeatNumber = null;
            this.stopHoldTimer();
            this.holdErrorMessage = null;
            this.adjacentGenderNotice = null;
            this.genderLocked = false;

            if (!this.selectedRouteId || !this.selectedDate) {
                this.schedules = [];
                return;
            }

            this.loadingSchedules = true;
            fetch('{{ route("user.api.schedules") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    route_id: this.selectedRouteId,
                    date: this.selectedDate
                })
            })
            .then(res => res.json())
            .then(data => {
                this.schedules = data;
                this.loadingSchedules = false;
            })
            .catch(() => { this.loadingSchedules = false; });
        },

        selectSchedule(sch) {
            this.selectedSchedule = sch;
            this.selectedSeatNumber = null;
            this.stopHoldTimer();
            this.holdErrorMessage = null;
            this.adjacentGenderNotice = null;
            this.genderLocked = false;

            if (sch.origin_city_id) this.fromCityId = sch.origin_city_id;
            if (sch.destination_city_id) this.toCityId = sch.destination_city_id;

            this.reloadSeatMap();
            this.fetchFare();
        },

        reloadSeatMap() {
            if (!this.selectedSchedule) return;

            fetch('{{ route("user.api.seatmap") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    schedule_id: this.selectedSchedule.id,
                    date: this.selectedDate
                })
            })
            .then(res => res.json())
            .then(data => {
                this.seatMap = data.seats;

                // Divide into main seats and continuous back row seats
                if (this.seatMap.length > 5) {
                    this.mainSeats = this.seatMap.slice(0, this.seatMap.length - 5);
                    this.backSeats = this.seatMap.slice(this.seatMap.length - 5);
                } else {
                    this.mainSeats = this.seatMap;
                    this.backSeats = [];
                }

                // If currently selected seat was booked or held by another, release selection
                if (this.selectedSeatNumber) {
                    const current = this.seatMap.find(s => s.number === this.selectedSeatNumber);
                    if (current && (current.is_booked || current.is_held_by_other)) {
                        this.selectedSeatNumber = null;
                        this.stopHoldTimer();
                        this.holdErrorMessage = "Your selected seat is no longer available.";
                    }
                }
            });
        },

        selectSeat(seat) {
            if (seat.is_booked) return;

            if (seat.is_held_by_other) {
                this.holdErrorMessage = `Seat #${seat.number} is currently locked by another counter operator. Please select an available seat.`;
                return;
            }

            // Clicked currently selected seat -> Toggle / Unhold
            if (this.selectedSeatNumber === seat.number) {
                fetch('{{ route("user.api.seatunhold") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        schedule_id: this.selectedSchedule.id,
                        booking_date: this.selectedDate,
                        seat_number: seat.number
                    })
                }).then(() => {
                    this.selectedSeatNumber = null;
                    this.adjacentGenderNotice = null;
                    this.genderLocked = false;
                    this.stopHoldTimer();
                    this.reloadSeatMap();
                });
                return;
            }

            // Lock seat via temporary hold API
            this.holdErrorMessage = null;
            fetch('{{ route("user.api.seathold") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    schedule_id: this.selectedSchedule.id,
                    booking_date: this.selectedDate,
                    seat_number: seat.number
                })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw data;
                }
                return data;
            })
            .then(data => {
                if (data.status === 'held') {
                    this.selectedSeatNumber = seat.number;
                    this.startHoldTimer(data.hold_seconds || 300);

                    // Gender policy restriction
                    if (seat.adjacent_gender === 'female') {
                        this.gender = 'female';
                        this.genderLocked = true;
                        this.adjacentGenderNotice = '⚠️ Adjacent seat is booked by a Female passenger. This seat is reserved for Female passengers.';
                    } else if (seat.adjacent_gender === 'male') {
                        this.gender = 'male';
                        this.genderLocked = true;
                        this.adjacentGenderNotice = '⚠️ Adjacent seat is booked by a Male passenger. This seat is reserved for Male passengers.';
                    } else {
                        this.genderLocked = false;
                        this.adjacentGenderNotice = null;
                    }

                    this.reloadSeatMap();
                } else if (data.status === 'unheld') {
                    this.selectedSeatNumber = null;
                    this.stopHoldTimer();
                    this.adjacentGenderNotice = null;
                    this.genderLocked = false;
                    this.reloadSeatMap();
                }
            })
            .catch(err => {
                this.holdErrorMessage = (err && err.message) ? err.message : 'Unable to lock this seat. It may have just been taken by another counter.';
                this.reloadSeatMap();
            });
        },

        fetchFare() {
            if (!this.selectedSchedule || !this.fromCityId || !this.toCityId) {
                this.calculatedFare = 0;
                return;
            }

            fetch('{{ route("user.api.fare") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    schedule_id: this.selectedSchedule.id,
                    from_city_id: this.fromCityId,
                    to_city_id: this.toCityId
                })
            })
            .then(res => res.json())
            .then(data => {
                this.calculatedFare = data.fare_amount || 1500;
            })
            .catch(() => {
                this.calculatedFare = 1500;
            });
        },

        formatTime(timeStr) {
            if (!timeStr) return '';
            const parts = timeStr.split(':');
            let hours = parseInt(parts[0]);
            const minutes = parts[1];
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            return `${hours}:${minutes} ${ampm}`;
        }
    }
}
</script>
@endsection
