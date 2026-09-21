@extends('layouts.app', ['title' => 'Print Ticket - #' . $booking->id])

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <!-- Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('user.dashboard') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Back to Counter Terminal
        </a>
        <button onclick="window.print()" class="px-5 py-2 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Print Ticket
        </button>
    </div>

    <!-- Ticket Card (Print Ready) -->
    <div class="bg-white rounded-3xl border-2 border-slate-900 shadow-xl overflow-hidden text-slate-800 p-6 sm:p-8 space-y-6 print:border-none print:shadow-none print:p-0">

        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-5">
            <div>
                <div class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-bus text-[#da1705]"></i>
                    <span>{{ $booking->schedule->route->company->name ?? 'Online Transport Service' }}</span>
                </div>
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Official Passenger Ticket</div>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black uppercase tracking-wider">
                    CONFIRMED
                </span>
                <div class="text-[11px] font-mono text-slate-500 mt-1">Ticket #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <!-- Journey Route Details -->
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Boarding Station</span>
                <span class="text-lg font-black text-slate-900">{{ $booking->fromCity->name ?? '' }}</span>
            </div>
            <div class="text-center text-slate-400">
                <i class="fa-solid fa-arrow-right-long text-xl text-[#da1705]"></i>
                <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mt-0.5">
                    {{ $booking->schedule->vehicleServiceType->name ?? '' }}
                </div>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Destination Station</span>
                <span class="text-lg font-black text-slate-900">{{ $booking->toCity->name ?? '' }}</span>
            </div>
        </div>

        <!-- Ticket Particulars Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 font-bold block uppercase text-[9px]">Passenger Name</span>
                <span class="font-black text-slate-900 text-sm block truncate">{{ $booking->passenger_name }}</span>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 font-bold block uppercase text-[9px]">Gender</span>
                <span class="font-bold text-slate-900 text-sm uppercase block">{{ $booking->gender }}</span>
            </div>

            <div class="p-3 bg-red-50/70 rounded-xl border border-red-100">
                <span class="text-[#da1705] font-bold block uppercase text-[9px]">Assigned Seat</span>
                <span class="font-black text-[#da1705] text-base block">Seat #{{ $booking->seat_number }}</span>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 font-bold block uppercase text-[9px]">Travel Date</span>
                <span class="font-bold text-slate-900 text-sm block">{{ $booking->booking_date->format('d M, Y') }}</span>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 font-bold block uppercase text-[9px]">Departure Time</span>
                <span class="font-bold text-slate-900 text-sm block">
                    {{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('h:i A') }}
                </span>
            </div>

            <div class="p-3 bg-emerald-50/70 rounded-xl border border-emerald-100">
                <span class="text-emerald-700 font-bold block uppercase text-[9px]">Total Fare Paid</span>
                <span class="font-black text-emerald-700 text-base block">Rs. {{ number_format($booking->fare_amount, 0) }}</span>
            </div>

            @if($booking->phone_number)
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 font-bold block uppercase text-[9px]">Contact Mobile</span>
                <span class="font-bold text-slate-900 text-xs block">{{ $booking->phone_number }}</span>
            </div>
            @endif

            @if($booking->cnic)
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 font-bold block uppercase text-[9px]">CNIC / ID Card</span>
                <span class="font-bold text-slate-900 text-xs block">{{ $booking->cnic }}</span>
            </div>
            @endif
        </div>

        <!-- Footer Bar -->
        <div class="pt-4 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-400 font-mono">
            <div>
                Issued By: {{ $booking->bookedByUser->name ?? 'Counter Terminal' }}
            </div>
            <div>
                Issued At: {{ $booking->created_at->format('Y-m-d H:i') }}
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
