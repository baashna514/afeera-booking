@extends('layouts.app', ['title' => 'Ticket Counter Terminal - Online Ticketing Software'])

@section('content')
<div class="space-y-4" x-data="ticketCounter()">

    <!-- Compact Top Counter Banner -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 text-slate-900 shadow-sm border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-[#da1705] flex items-center justify-center text-lg font-black shrink-0 border border-red-100">
                <i class="fa-solid fa-desktop"></i>
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-black tracking-tight text-slate-900 flex items-center gap-2">
                    <span>Counter Terminal: <span class="text-[#da1705]">{{ auth()->user()->name }}</span></span>
                </h1>
                <p class="text-xs font-semibold text-slate-500">Pick route, departure time, and select seats directly from the interactive map.</p>
            </div>
        </div>
        <div class="flex items-center gap-3 self-end md:self-auto">
            <div class="bg-slate-900 px-3.5 py-1.5 rounded-xl text-right text-white">
                <span class="text-[9px] font-bold text-slate-400 uppercase block">Today Sales</span>
                <span class="text-sm font-black text-white">Rs. {{ number_format($todayRevenue ?? 0, 0) }}</span>
            </div>
            <div class="bg-slate-900 px-3.5 py-1.5 rounded-xl text-right text-white">
                <span class="text-[9px] font-bold text-slate-400 uppercase block">Tickets</span>
                <span class="text-sm font-black text-[#da1705]">{{ $todayBookings ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Step 1: Compact Route & Date Selection Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <!-- Route Selector -->
            <div class="md:col-span-6">
                <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1">Select Bus Route *</label>
                <select x-model="selectedRouteId" @change="onRouteOrDateChange()" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] focus:border-[#da1705] text-xs font-bold text-slate-800 bg-white">
                    <option value="">-- Choose Route --</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}">
                            {{ $route->name }} ({{ $route->originCity->name ?? '' }} → {{ $route->destinationCity->name ?? '' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Selector -->
            <div class="md:col-span-4">
                <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1">Travel Date *</label>
                <input type="date" x-model="selectedDate" min="{{ date('Y-m-d') }}" @change="onRouteOrDateChange()"
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-xs font-bold text-slate-800">
            </div>

            <!-- Refresh Button -->
            <div class="md:col-span-2">
                <button type="button" @click="reloadSeatMap()" 
                    class="w-full px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer"
                    title="Refresh Seat Map from Database">
                    <i class="fa-solid fa-rotate-right text-[#da1705]" :class="isRefreshing ? 'animate-spin' : ''"></i>
                    <span>Refresh Seats</span>
                </button>
            </div>
        </div>

        <!-- Available Departure Schedules Chips -->
        <div x-show="selectedRouteId && selectedDate" class="pt-2 border-t border-slate-100">
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700">Available Departure Times:</label>
                <span class="text-[10px] text-slate-400 font-medium">Click time to load seats</span>
            </div>

            <template x-if="loadingSchedules">
                <div class="py-2 text-center text-xs text-slate-400 font-medium">
                    <i class="fa-solid fa-spinner animate-spin mr-1 text-[#da1705]"></i> Loading departure schedules...
                </div>
            </template>

            <template x-if="!loadingSchedules && schedules.length === 0">
                <div class="p-2.5 bg-amber-50 rounded-xl text-xs text-amber-800 border border-amber-200 font-semibold">
                    No active departure schedules found for this route on the selected date.
                </div>
            </template>

            <div class="flex flex-wrap gap-2.5">
                <template x-for="sch in schedules" :key="sch.id">
                    <button type="button" @click="selectSchedule(sch)"
                        :class="selectedSchedule && selectedSchedule.id === sch.id ? 'border-[#da1705] bg-red-50 ring-2 ring-red-500/30 text-slate-900 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-slate-50 text-slate-700'"
                        class="px-3.5 py-2 rounded-xl border transition flex items-center gap-3 cursor-pointer text-left">
                        <div>
                            <div class="font-mono font-black text-xs flex items-center gap-1">
                                <i class="fa-solid fa-clock text-[#da1705] text-[10px]"></i>
                                <span x-text="formatTime(sch.departure_time)"></span>
                            </div>
                            <div class="text-[10px] font-bold text-slate-500" x-text="sch.vehicle_type"></div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0"
                            :class="sch.available_seats > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'">
                            <span x-text="sch.available_seats"></span> seats left
                        </span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- Hold Error / Conflict Message Banner -->
    <template x-if="holdErrorMessage">
        <div class="p-3 bg-red-50 border border-red-200 text-[#da1705] rounded-xl text-xs font-bold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-base shrink-0"></i>
                <span x-text="holdErrorMessage"></span>
            </div>
            <button type="button" @click="holdErrorMessage = null" class="text-red-400 hover:text-red-700">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </template>

    <!-- Empty State Prompt -->
    <div x-show="!selectedSchedule" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-10 text-center">
        <div class="w-14 h-14 rounded-2xl bg-red-50 text-[#da1705] flex items-center justify-center text-xl mx-auto mb-2 border border-red-100">
            <i class="fa-solid fa-bus-simple"></i>
        </div>
        <h4 class="text-base font-black text-slate-800">Select Journey & Departure Schedule Above</h4>
        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto font-medium">
            Select a bus route, travel date, and departure time from the top bar to load the side-by-side booking terminal and seat map.
        </p>
    </div>

    <!-- Step 2: DUAL COLUMN WORKSPACE (LEFT: Passenger Info Form | RIGHT: Interactive Seat Map) -->
    <div x-show="selectedSchedule" class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

        <!-- LEFT COLUMN (Passenger Information & Checkout Form) -->
        <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-black text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-user-pen text-[#da1705]"></i>
                    <span>Passenger Information & Ticket Issue</span>
                </h3>
                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-red-50 text-[#da1705]" x-text="selectedSchedule ? selectedSchedule.vehicle_type : ''"></span>
            </div>

            <form action="{{ route('user.booking.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="schedule_id" :value="selectedSchedule ? selectedSchedule.id : ''">
                <input type="hidden" name="booking_date" :value="selectedDate">
                <input type="hidden" name="seat_number" :value="selectedSeatNumber">
                <input type="hidden" name="from_city_id" :value="fromCityId">
                <input type="hidden" name="to_city_id" :value="toCityId">
                <input type="hidden" name="fare_amount" :value="calculatedFare > 0 ? calculatedFare : 1500">

                <!-- Selected Seat & Fare Badge -->
                <div class="bg-red-50/80 p-3.5 rounded-xl border border-red-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider block">Selected Seat</span>
                        <span class="text-xl font-black text-[#da1705]" x-text="selectedSeatNumber ? 'Seat # ' + selectedSeatNumber : 'Click a seat on map ➔'"></span>
                        <template x-if="holdTimeRemaining > 0 && selectedSeatNumber">
                            <span class="text-[10px] font-bold text-amber-900 bg-amber-100 border border-amber-300 px-2 py-0.5 rounded-full inline-flex items-center gap-1 mt-1">
                                <i class="fa-solid fa-stopwatch animate-pulse text-[#da1705]"></i> Locked: <span class="font-mono" x-text="formattedHoldTimer"></span>
                            </span>
                        </template>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider block">Ticket Fare</span>
                        <span class="text-xl font-black text-emerald-600" x-text="calculatedFare ? 'Rs. ' + calculatedFare : 'Rs. 1,500'"></span>
                    </div>
                </div>

                <!-- Adjacent Gender Policy Warning Notice -->
                <template x-if="adjacentGenderNotice">
                    <div class="p-2.5 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-xs font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-person-half-dress text-amber-600 text-base shrink-0"></i>
                        <span x-text="adjacentGenderNotice"></span>
                    </div>
                </template>

                <!-- Input Fields Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1">Passenger Name *</label>
                        <input type="text" name="passenger_name" x-model="passengerName" placeholder="Enter full name" required
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-xs font-semibold text-slate-800">
                    </div>

                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1">Mobile Phone # *</label>
                        <input type="text" name="phone_number" x-model="phoneNumber" placeholder="0300-1234567" required
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-xs font-semibold text-slate-800">
                    </div>

                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1">CNIC / ID (Optional)</label>
                        <input type="text" name="cnic" x-model="cnic" placeholder="32102-xxxxxxx-x"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-xs font-semibold text-slate-800">
                    </div>

                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1">Passenger Gender *</label>
                        <select name="gender" x-model="gender" :disabled="genderLocked" required 
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-xs font-bold text-slate-800 bg-white disabled:bg-slate-100 disabled:text-slate-500">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        <template x-if="genderLocked">
                            <input type="hidden" name="gender" :value="gender">
                        </template>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                    <button type="button" @click="showExpenseModal = true"
                        class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-calculator text-[#da1705]"></i>
                        <span>+ Record Expenses & Voucher</span>
                    </button>

                    <button type="submit" :disabled="!selectedSeatNumber"
                        class="px-6 py-2.5 bg-[#da1705] hover:bg-[#b91204] disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs rounded-xl shadow-md shadow-red-900/20 transition flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-print"></i>
                        <span>Issue & Print Ticket</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT COLUMN (Interactive Bus Seat Map) -->
        <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-black text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-chair text-[#da1705]"></i>
                    <span>Bus Seat Layout Map (Right to Left)</span>
                </h3>
                <span class="text-[10px] font-bold text-slate-400">Total: <span class="text-slate-800" x-text="seatMap.length"></span> Seats</span>
            </div>

            <!-- Bus Body Container -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 max-w-sm mx-auto space-y-3">
                
                <!-- Driver Cabin & Entrance Row -->
                <div class="flex items-center justify-between pb-2 border-b-2 border-dashed border-slate-300">
                    <div class="px-2.5 py-1 bg-slate-200 text-slate-700 rounded-lg text-[10px] font-bold flex items-center gap-1">
                        <i class="fa-solid fa-door-open text-slate-500"></i> Entrance
                    </div>
                    <div class="px-2.5 py-1 bg-slate-800 text-white rounded-lg text-[10px] font-black flex items-center gap-1">
                        <i class="fa-solid fa-dharmachakra text-[#da1705]"></i> Driver
                    </div>
                </div>

                <!-- Seat Map Loading State -->
                <template x-if="loadingSeatMap">
                    <div class="py-12 text-center text-xs text-slate-400 font-medium">
                        <i class="fa-solid fa-spinner animate-spin text-[#da1705] mr-1"></i> Loading seats...
                    </div>
                </template>

                <!-- Dynamic Seat Rows (Right to Left Layout) -->
                <div x-show="!loadingSeatMap" class="space-y-2">
                    <template x-for="(row, rIdx) in seatRows" :key="rIdx">
                        <div>
                            <!-- Standard Row: 2 Right Seats | Aisle | 2 Left Seats -->
                            <template x-if="row.type === 'standard'">
                                <div class="grid grid-cols-5 gap-2 items-center" dir="rtl">
                                    <!-- Right Seat 1 (Window) -->
                                    <template x-if="row.rightSeats[0]">
                                        <button type="button"
                                            @click="selectSeat(row.rightSeats[0])"
                                            :disabled="row.rightSeats[0].is_booked || row.rightSeats[0].is_held_by_other"
                                            :title="getSeatTooltip(row.rightSeats[0])"
                                            :class="getSeatClass(row.rightSeats[0])"
                                            class="h-9 rounded-lg flex items-center justify-center font-bold text-xs transition relative">
                                            <span x-text="row.rightSeats[0].number"></span>
                                            <template x-if="row.rightSeats[0].is_held_by_other">
                                                <i class="fa-solid fa-lock text-[8px] absolute top-1 right-1 text-amber-100"></i>
                                            </template>
                                        </button>
                                    </template>

                                    <!-- Right Seat 2 (Aisle) -->
                                    <template x-if="row.rightSeats[1]">
                                        <button type="button"
                                            @click="selectSeat(row.rightSeats[1])"
                                            :disabled="row.rightSeats[1].is_booked || row.rightSeats[1].is_held_by_other"
                                            :title="getSeatTooltip(row.rightSeats[1])"
                                            :class="getSeatClass(row.rightSeats[1])"
                                            class="h-9 rounded-lg flex items-center justify-center font-bold text-xs transition relative">
                                            <span x-text="row.rightSeats[1].number"></span>
                                            <template x-if="row.rightSeats[1].is_held_by_other">
                                                <i class="fa-solid fa-lock text-[8px] absolute top-1 right-1 text-amber-100"></i>
                                            </template>
                                        </button>
                                    </template>

                                    <!-- Middle Aisle Gap -->
                                    <div class="h-9 flex items-center justify-center text-[10px] text-slate-300 font-mono select-none">
                                        •
                                    </div>

                                    <!-- Left Seat 1 (Aisle) -->
                                    <template x-if="row.leftSeats[0]">
                                        <button type="button"
                                            @click="selectSeat(row.leftSeats[0])"
                                            :disabled="row.leftSeats[0].is_booked || row.leftSeats[0].is_held_by_other"
                                            :title="getSeatTooltip(row.leftSeats[0])"
                                            :class="getSeatClass(row.leftSeats[0])"
                                            class="h-9 rounded-lg flex items-center justify-center font-bold text-xs transition relative">
                                            <span x-text="row.leftSeats[0].number"></span>
                                            <template x-if="row.leftSeats[0].is_held_by_other">
                                                <i class="fa-solid fa-lock text-[8px] absolute top-1 right-1 text-amber-100"></i>
                                            </template>
                                        </button>
                                    </template>

                                    <!-- Left Seat 2 (Window) -->
                                    <template x-if="row.leftSeats[1]">
                                        <button type="button"
                                            @click="selectSeat(row.leftSeats[1])"
                                            :disabled="row.leftSeats[1].is_booked || row.leftSeats[1].is_held_by_other"
                                            :title="getSeatTooltip(row.leftSeats[1])"
                                            :class="getSeatClass(row.leftSeats[1])"
                                            class="h-9 rounded-lg flex items-center justify-center font-bold text-xs transition relative">
                                            <span x-text="row.leftSeats[1].number"></span>
                                            <template x-if="row.leftSeats[1].is_held_by_other">
                                                <i class="fa-solid fa-lock text-[8px] absolute top-1 right-1 text-amber-100"></i>
                                            </template>
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <!-- Continuous Back Row (5 seats across without gap) -->
                            <template x-if="row.type === 'back'">
                                <div class="mt-2 pt-2 border-t-2 border-dashed border-slate-300">
                                    <div class="text-[9px] font-bold text-slate-400 text-center uppercase mb-1.5">Back Row</div>
                                    <div class="grid grid-cols-5 gap-2" dir="rtl">
                                        <template x-for="seat in row.seats" :key="seat.number">
                                            <button type="button"
                                                @click="selectSeat(seat)"
                                                :disabled="seat.is_booked || seat.is_held_by_other"
                                                :title="getSeatTooltip(seat)"
                                                :class="getSeatClass(seat)"
                                                class="h-9 rounded-lg flex items-center justify-center font-bold text-xs transition relative">
                                                <span x-text="seat.number"></span>
                                                <template x-if="seat.is_held_by_other">
                                                    <i class="fa-solid fa-lock text-[8px] absolute top-1 right-1 text-amber-100"></i>
                                                </template>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Seat Legend -->
            <div class="flex items-center justify-center gap-3 text-[11px] font-semibold flex-wrap pt-2 border-t border-slate-100">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-emerald-500"></span> Available</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-[#da1705]"></span> Selected</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-amber-500"></span> Locked</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-pink-500"></span> Female</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-slate-700"></span> Male</span>
            </div>
        </div>

    </div>

    <!-- Terminal Expenses & Trip Dispatch Voucher Modal -->
    <div x-show="showExpenseModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 border border-slate-200 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-black text-slate-900 text-base flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-[#da1705]"></i>
                        <span>Terminal Expenses & Dispatch Voucher</span>
                    </h3>
                    <p class="text-xs text-slate-500">Add dynamic terminal expenses to compute net handover collection.</p>
                </div>
                <button type="button" @click="showExpenseModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('user.terminal.voucher') }}" method="POST" target="_blank" class="space-y-4">
                @csrf
                <input type="hidden" name="schedule_id" :value="selectedSchedule ? selectedSchedule.id : ''">
                <input type="hidden" name="booking_date" :value="selectedDate">
                <input type="hidden" name="seat_number" :value="selectedSeatNumber">
                <input type="hidden" name="from_city_id" :value="fromCityId">
                <input type="hidden" name="to_city_id" :value="toCityId">
                <input type="hidden" name="passenger_name" :value="passengerName">
                <input type="hidden" name="phone_number" :value="phoneNumber">
                <input type="hidden" name="cnic" :value="cnic">
                <input type="hidden" name="gender" :value="gender">
                <input type="hidden" name="fare_amount" :value="calculatedFare > 0 ? calculatedFare : 1500">

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1">Terminal Station Name</label>
                    <input type="text" name="terminal_city" placeholder="e.g. Layyah Terminal / ShorKot Counter"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705]">
                </div>

                <!-- Dynamic Expenses Adder -->
                <div class="space-y-2.5 pt-1">
                    <div class="flex items-center justify-between">
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700">Dynamic Expenses List</label>
                        <button type="button" @click="addExpenseRow()" class="text-xs font-bold text-[#da1705] hover:underline flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-plus-circle"></i> + Add Expense Row
                        </button>
                    </div>

                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        <template x-for="(exp, index) in expenseRows" :key="index">
                            <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-xl border border-slate-200">
                                <div class="flex-1">
                                    <select :name="'expenses[' + index + '][expense_type_id]'" x-model="exp.expense_type_id" class="w-full px-2.5 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 bg-white focus:ring-2 focus:ring-[#da1705]">
                                        <option value="">-- Select Expense Title --</option>
                                        @foreach($expenseTypes ?? [] as $expType)
                                            <option value="{{ $expType->id }}">{{ $expType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-28">
                                    <input type="number" step="10" min="0" :name="'expenses[' + index + '][amount]'" x-model="exp.amount" placeholder="Rs. Amount"
                                        class="w-full px-2.5 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 focus:ring-2 focus:ring-[#da1705]">
                                </div>
                                <button type="button" @click="removeExpenseRow(index)" class="w-7 h-7 flex items-center justify-center text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Remove Row">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Optional Staff Allowances -->
                <div class="pt-2 border-t border-slate-100 space-y-2">
                    <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700">Trip Staff Fees (Driver / Guard / Hostess)</label>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div>
                            <span class="block text-[10px] font-bold text-slate-500 mb-0.5">Driver Fee</span>
                            <input type="number" step="50" min="0" name="driver_allowance" placeholder="Rs. 0" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-xs">
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-slate-500 mb-0.5">Guard Fee</span>
                            <input type="number" step="50" min="0" name="guard_allowance" placeholder="Rs. 0" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-xs">
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-slate-500 mb-0.5">Hostess Fee</span>
                            <input type="number" step="50" min="0" name="hostess_allowance" placeholder="Rs. 0" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-xs">
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showExpenseModal = false" class="px-4 py-2 text-xs font-bold text-slate-600">Cancel</button>
                    <button type="submit" @click="showExpenseModal = false"
                        class="px-5 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-file-contract"></i> Save & Generate Voucher
                    </button>
                </div>
            </form>
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
        seatRows: [],
        loadingSeatMap: false,
        isRefreshing: false,
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
            // Live background polling every 10s to sync seat holds across all counter terminals
            setInterval(() => {
                if (this.selectedSchedule && !this.loadingSeatMap) {
                    this.fetchSeatMapData(false);
                }
            }, 10000);
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
                    this.holdErrorMessage = "Temporary seat lock has expired. Please select a seat again.";
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
            this.seatRows = [];
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
            this.isRefreshing = true;
            this.fetchSeatMapData(true).finally(() => {
                setTimeout(() => { this.isRefreshing = false; }, 400);
            });
        },

        fetchSeatMapData(showLoading = false) {
            if (!this.selectedSchedule) return Promise.resolve();
            if (showLoading) this.loadingSeatMap = true;

            return fetch('{{ route("user.api.seatmap") }}', {
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
                this.seatMap = data.seats || [];
                this.buildSeatRows();

                // If currently selected seat was booked or held by another, release selection
                if (this.selectedSeatNumber) {
                    const current = this.seatMap.find(s => s.number === this.selectedSeatNumber);
                    if (current && (current.is_booked || current.is_held_by_other)) {
                        this.selectedSeatNumber = null;
                        this.stopHoldTimer();
                        this.holdErrorMessage = "Your selected seat was just reserved/booked by another counter.";
                    }
                }
            })
            .finally(() => {
                this.loadingSeatMap = false;
            });
        },

        buildSeatRows() {
            const total = this.seatMap.length;
            if (total === 0) {
                this.seatRows = [];
                return;
            }

            // In Pakistani buses, standard rows have 4 seats (2 right, 2 left).
            // If total % 4 === 1 (e.g. 37, 41, 45 seats), the last row is a continuous 5-seat back row.
            const hasBackRow5 = (total % 4 === 1 && total >= 5);
            const mainCount = hasBackRow5 ? (total - 5) : (Math.floor(total / 4) * 4);

            let rows = [];

            // Build standard 4-seat rows (Right Window, Right Aisle | Aisle | Left Aisle, Left Window)
            for (let i = 0; i < mainCount; i += 4) {
                rows.push({
                    type: 'standard',
                    rightSeats: [this.seatMap[i], this.seatMap[i + 1]].filter(Boolean),
                    leftSeats: [this.seatMap[i + 2], this.seatMap[i + 3]].filter(Boolean),
                });
            }

            // Build back row with remaining seats (e.g. seats 33, 34, 35, 36, 37 straight across)
            if (total > mainCount) {
                rows.push({
                    type: 'back',
                    seats: this.seatMap.slice(mainCount)
                });
            }

            this.seatRows = rows;
        },

        getSeatClass(seat) {
            if (!seat) return '';
            if (this.selectedSeatNumber === seat.number) {
                return 'bg-[#da1705] text-white font-black ring-4 ring-red-300 scale-105 shadow-md';
            }
            if (seat.is_held_by_other) {
                return 'bg-amber-500 text-white cursor-not-allowed opacity-90 shadow-sm';
            }
            if (seat.is_booked) {
                if (seat.gender === 'female') return 'bg-pink-500 text-white cursor-not-allowed';
                if (seat.gender === 'male') return 'bg-slate-700 text-white cursor-not-allowed';
                return 'bg-slate-500 text-slate-100 cursor-not-allowed';
            }
            return 'bg-emerald-500 text-white hover:bg-emerald-600 shadow-xs cursor-pointer';
        },

        getSeatTooltip(seat) {
            if (!seat) return '';
            if (seat.is_held_by_other) return `Seat #${seat.number} is locked by another terminal`;
            if (seat.is_booked) return `Seat #${seat.number} is already booked (${seat.gender || 'passenger'})`;
            return `Seat #${seat.number} (Available)`;
        },

        selectSeat(seat) {
            if (!seat || seat.is_booked) return;

            if (seat.is_held_by_other) {
                this.holdErrorMessage = `Seat #${seat.number} is currently locked by another counter operator. Please pick an available seat.`;
                return;
            }

            // Toggle / Unhold if clicked again
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
                    this.fetchSeatMapData(false);
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
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                if (data.status === 'held') {
                    this.selectedSeatNumber = seat.number;
                    this.startHoldTimer(data.hold_seconds || 300);

                    // Gender policy restrictions
                    if (seat.adjacent_gender === 'female') {
                        this.gender = 'female';
                        this.genderLocked = true;
                        this.adjacentGenderNotice = '⚠️ Adjacent seat is booked by a Female passenger. By policy, this seat is reserved for Female passengers.';
                    } else if (seat.adjacent_gender === 'male') {
                        this.gender = 'male';
                        this.genderLocked = true;
                        this.adjacentGenderNotice = '⚠️ Adjacent seat is booked by a Male passenger. By policy, this seat is reserved for Male passengers.';
                    } else {
                        this.genderLocked = false;
                        this.adjacentGenderNotice = null;
                    }

                    this.fetchSeatMapData(false);
                } else if (data.status === 'unheld') {
                    this.selectedSeatNumber = null;
                    this.stopHoldTimer();
                    this.adjacentGenderNotice = null;
                    this.genderLocked = false;
                    this.fetchSeatMapData(false);
                }
            })
            .catch(err => {
                this.holdErrorMessage = (err && err.message) ? err.message : 'Unable to lock this seat. It may have just been selected by another counter.';
                this.fetchSeatMapData(false);
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
