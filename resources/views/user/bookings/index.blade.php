@extends('layouts.app', ['title' => 'Issued Tickets History - Counter Terminal'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-[#da1705]"></i>
                <span>Issued Tickets History</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">View, search, reprint, or cancel ticket reservations issued at the counter.</p>
        </div>
        <div>
            <a href="{{ route('user.dashboard') }}" class="px-5 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-lg shadow-red-900/20 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> New Ticket Booking
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
        <form action="{{ route('user.booking.history') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Search Ticket / Passenger</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Passenger name or Ticket #"
                        class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-xs text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Filter Travel Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-xs text-slate-800">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Apply Filter
                </button>
                @if(request()->hasAny(['search', 'date']))
                    <a href="{{ route('user.booking.history') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
        @if($bookings->isEmpty())
            <div class="py-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-[#da1705] flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <h4 class="text-base font-bold text-slate-800">No Tickets Issued Found</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Tickets issued by counter staff will appear here with complete details.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">Ticket #</th>
                            <th class="px-4 py-3">Passenger</th>
                            <th class="px-4 py-3">Route (From → To)</th>
                            <th class="px-4 py-3">Travel Date & Departure</th>
                            <th class="px-4 py-3">Seat #</th>
                            <th class="px-4 py-3">Fare (PKR)</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right rounded-r-xl">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $booking->passenger_name }}</div>
                                    <div class="text-[10px] uppercase font-semibold text-slate-400">{{ $booking->gender }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="inline-flex items-center gap-1 font-bold text-slate-800">
                                        <span>{{ $booking->fromCity->name ?? '—' }}</span>
                                        <i class="fa-solid fa-arrow-right text-[10px] text-slate-400"></i>
                                        <span>{{ $booking->toCity->name ?? '—' }}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <div class="font-bold text-slate-800">{{ $booking->booking_date->format('d M, Y') }}</div>
                                    <div class="text-[11px] font-mono text-slate-500">
                                        <i class="fa-solid fa-clock text-[#da1705] text-[10px]"></i>
                                        {{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('h:i A') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs font-black text-indigo-700">Seat #{{ $booking->seat_number }}</td>
                                <td class="px-4 py-3 font-mono font-bold text-emerald-600 text-sm">Rs. {{ number_format($booking->fare_amount, 0) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $booking->status === 'confirmed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ $booking->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-1.5">
                                    <a href="{{ route('user.booking.ticket', $booking) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-print text-[11px]"></i> Print
                                    </a>
                                    @if($booking->status === 'confirmed')
                                        <form action="{{ route('user.booking.cancel', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel ticket #{{ $booking->id }}?')">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-lg transition inline-flex items-center gap-1">
                                                <i class="fa-solid fa-ban text-[11px]"></i> Cancel
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
