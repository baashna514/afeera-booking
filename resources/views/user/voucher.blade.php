@extends('layouts.app', ['title' => 'Trip Terminal Dispatch Voucher'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('user.dashboard') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Back to Terminal
        </a>
        <button onclick="window.print()" class="px-5 py-2 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Print Voucher
        </button>
    </div>

    <!-- Official Trip Dispatch Voucher (Print Ready) -->
    <div class="bg-white rounded-3xl border-2 border-slate-900 shadow-xl overflow-hidden text-slate-800 p-6 sm:p-8 space-y-6 print:border-none print:shadow-none print:p-0">

        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-5">
            <div>
                <div class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    @include('components.application-logo', ['class' => 'w-8 h-8'])
                    <span>{{ $schedule->route->company->name ?? 'Online Transport Service' }}</span>
                </div>
                <div class="text-xs font-bold text-[#da1705] uppercase tracking-wider mt-1">
                    Terminal Trip Dispatch Voucher
                </div>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full bg-slate-900 text-white text-xs font-black uppercase tracking-wider">
                    DISPATCH VOUCHER
                </span>
                <div class="text-[11px] font-mono text-slate-500 mt-1">Voucher Date: {{ date('d M, Y') }}</div>
            </div>
        </div>

        <!-- Trip Details Summary -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-200">
            <div>
                <span class="text-slate-400 font-bold block uppercase text-[9px]">Terminal Station</span>
                <span class="font-black text-slate-900 text-sm">{{ $terminalCity ?? 'Main Terminal' }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-bold block uppercase text-[9px]">Bus & Reg #</span>
                <span class="font-bold text-slate-900 text-sm">
                    {{ $schedule->vehicle ? "Bus {$schedule->vehicle->bus_number} ({$schedule->vehicle->registration_number})" : 'Assigned Fleet' }}
                </span>
            </div>
            <div>
                <span class="text-slate-400 font-bold block uppercase text-[9px]">Route</span>
                <span class="font-bold text-slate-900 truncate block">{{ $schedule->route->name ?? '—' }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-bold block uppercase text-[9px]">Departure Time</span>
                <span class="font-bold text-slate-900 font-mono">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('h:i A') }}</span>
            </div>
        </div>

        <!-- Seats Issued at this Terminal -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center justify-between">
                <span>Passengers / Seats Issued at Terminal</span>
                <span class="text-indigo-700 font-mono">{{ $bookings->count() }} Seats</span>
            </h4>

            @if($bookings->isEmpty())
                <div class="p-4 bg-slate-50 rounded-xl text-center text-xs text-slate-400 italic">
                    No seats issued at this terminal for this trip.
                </div>
            @else
                <table class="w-full text-left text-xs border divide-y divide-slate-200 rounded-xl overflow-hidden">
                    <thead class="bg-slate-100 text-slate-600 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="p-2.5">Seat #</th>
                            <th class="p-2.5">Passenger</th>
                            <th class="p-2.5">Gender</th>
                            <th class="p-2.5">From → To</th>
                            <th class="p-2.5 text-right">Fare (PKR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($bookings as $b)
                            <tr>
                                <td class="p-2.5 font-bold font-mono text-indigo-700">Seat #{{ $b->seat_number }}</td>
                                <td class="p-2.5 font-bold text-slate-900">{{ $b->passenger_name }}</td>
                                <td class="p-2.5 uppercase text-[10px]">{{ $b->gender }}</td>
                                <td class="p-2.5 text-slate-600">{{ $b->fromCity->name ?? '' }} → {{ $b->toCity->name ?? '' }}</td>
                                <td class="p-2.5 text-right font-mono font-bold text-slate-900">Rs. {{ number_format($b->fare_amount, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Terminal Expenses Breakdown -->
        <div class="space-y-3 pt-2">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Terminal Expenses Breakdown</h4>

            @if(!empty($expensesList) && count($expensesList) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                    @foreach($expensesList as $exp)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-slate-400 font-bold block uppercase text-[9px]">{{ $exp['title'] }}</span>
                            <span class="font-mono font-bold text-slate-900">Rs. {{ number_format($exp['amount'], 0) }}</span>
                        </div>
                    @endforeach
                    <div class="p-3 bg-rose-50 rounded-xl border border-rose-200">
                        <span class="text-[#da1705] font-bold block uppercase text-[9px]">Total Expenses</span>
                        <span class="font-mono font-black text-[#da1705]">Rs. {{ number_format($totalExpenses ?? 0, 0) }}</span>
                    </div>
                </div>
            @else
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-500 italic">
                    No terminal expenses recorded for this trip. Total Expenses: Rs. 0
                </div>
            @endif
        </div>

        @php
            $netCollection = ($totalRevenue ?? 0) - ($totalExpenses ?? 0);
        @endphp

        <!-- Net Cash Summary Card -->
        <div class="p-5 {{ $netCollection < 0 ? 'bg-amber-950 border border-amber-800' : 'bg-slate-900' }} text-white rounded-2xl flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Ticket Revenue</span>
                <span class="text-xl font-mono font-bold text-white">Rs. {{ number_format($totalRevenue ?? 0, 0) }}</span>
            </div>
            <div class="text-center border-x border-slate-700/60 px-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-400 block">Total Expenses</span>
                <span class="text-xl font-mono font-bold text-rose-400">Rs. {{ number_format($totalExpenses ?? 0, 0) }}</span>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $netCollection >= 0 ? 'text-emerald-400' : 'text-amber-400' }} block">Net Collection Handover</span>
                <span class="text-2xl font-mono font-black {{ $netCollection >= 0 ? 'text-emerald-400' : 'text-amber-400' }}">
                    {{ $netCollection < 0 ? '-Rs. ' . number_format(abs($netCollection), 0) : 'Rs. ' . number_format($netCollection, 0) }}
                </span>
            </div>
        </div>

        @if($netCollection < 0)
            <div class="p-3.5 bg-amber-50 rounded-xl border border-amber-200 text-xs font-bold text-amber-900 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base"></i>
                <span>Notice: Total expenses exceed ticket revenue at this terminal by Rs. {{ number_format(abs($netCollection), 0) }}.</span>
            </div>
        @endif

        <!-- Signatures Bar -->
        <div class="pt-8 grid grid-cols-2 gap-8 text-center text-xs font-bold text-slate-500 border-t border-slate-200">
            <div>
                <div class="border-b-2 border-slate-300 pb-8 mb-1"></div>
                <span>Counter Operator Signature</span>
            </div>
            <div>
                <div class="border-b-2 border-slate-300 pb-8 mb-1"></div>
                <span>Bus Guard / Driver Signature</span>
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    .no-print, nav, footer {
        display: none !important;
    }
    body {
        background: white !important;
    }
}
</style>
@endsection
