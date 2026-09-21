<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Fare;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\SeatHold;
use App\Models\TerminalExpenseType;
use App\Models\Voucher;
use App\Models\VoucherExpense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Show the ticket counter dashboard / booking form.
     */
    public function index(): View
    {
        $routes = Route::with(['originCity', 'destinationCity'])
            ->where('status', 'active')
            ->get();

        $todayBookings = Booking::where('booked_by', auth()->id())
            ->whereDate('booking_date', today())
            ->count();

        $todayRevenue = Booking::where('booked_by', auth()->id())
            ->whereDate('booking_date', today())
            ->where('status', 'confirmed')
            ->sum('fare_amount');

        $expenseTypes = TerminalExpenseType::where('status', 'active')->orderBy('name')->get();

        return view('user.dashboard', compact('routes', 'todayBookings', 'todayRevenue', 'expenseTypes'));
    }

    /**
     * Show list of issued bookings / history for the counter terminal.
     */
    public function history(Request $request): View
    {
        $query = Booking::with(['schedule.route.originCity', 'schedule.route.destinationCity', 'schedule.vehicleServiceType', 'fromCity', 'toCity', 'bookedByUser'])
            ->latest();

        // Optional filter by search query (passenger name or ticket ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('passenger_name', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        // Optional filter by travel date
        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->paginate(15)->withQueryString();

        return view('user.bookings.index', compact('bookings'));
    }

    /**
     * Generate Trip Dispatch Voucher for bus guard / driver with terminal expenses.
     */
    public function generateVoucher(Request $request): View
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'booking_date' => 'required|date',
            'terminal_city' => 'nullable|string',
            'seat_number' => 'nullable|integer|min:1',
            'from_city_id' => 'nullable|exists:cities,id',
            'to_city_id' => 'nullable|exists:cities,id',
            'passenger_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:30',
            'cnic' => 'nullable|string|max:30',
            'gender' => 'nullable|in:male,female',
            'fare_amount' => 'nullable|numeric|min:0',
            'driver_allowance' => 'nullable|numeric|min:0',
            'guard_allowance' => 'nullable|numeric|min:0',
            'hostess_allowance' => 'nullable|numeric|min:0',
            'is_final_voucher' => 'nullable|boolean',
            'expenses' => 'nullable|array',
            'expenses.*.expense_type_id' => 'nullable',
            'expenses.*.custom_name' => 'nullable|string',
            'expenses.*.amount' => 'nullable|numeric|min:0',
        ]);

        $schedule = Schedule::with(['route.company', 'route.originCity', 'route.destinationCity', 'vehicleServiceType', 'vehicle'])
            ->findOrFail($request->schedule_id);

        // 1. Auto-save ticket if ticket form fields were filled in prior to voucher generation
        if ($request->filled('seat_number') && $request->filled('passenger_name') && $request->filled('from_city_id') && $request->filled('to_city_id')) {
            $alreadyBooked = Booking::where('schedule_id', $schedule->id)
                ->whereDate('booking_date', $request->booking_date)
                ->where('seat_number', $request->seat_number)
                ->where('status', 'confirmed')
                ->exists();

            if (! $alreadyBooked) {
                Booking::create([
                    'schedule_id' => $schedule->id,
                    'booking_date' => $request->booking_date,
                    'seat_number' => $request->seat_number,
                    'from_city_id' => $request->from_city_id,
                    'to_city_id' => $request->to_city_id,
                    'passenger_name' => $request->passenger_name,
                    'phone_number' => $request->phone_number,
                    'cnic' => $request->cnic,
                    'gender' => $request->gender ?? 'male',
                    'fare_amount' => $request->fare_amount ?? 1500,
                    'status' => 'confirmed',
                    'booked_by' => auth()->id(),
                ]);

                SeatHold::where('schedule_id', $schedule->id)
                    ->whereDate('booking_date', $request->booking_date)
                    ->where('seat_number', $request->seat_number)
                    ->delete();
            }
        }

        // 2. Fetch all confirmed bookings for this schedule & travel date
        $bookings = Booking::with(['fromCity', 'toCity'])
            ->where('schedule_id', $schedule->id)
            ->whereDate('booking_date', $request->booking_date)
            ->where('status', 'confirmed')
            ->orderBy('seat_number')
            ->get();

        $expensesList = [];

        // Support dynamic expenses array
        if (! empty($request->expenses) && is_array($request->expenses)) {
            foreach ($request->expenses as $expItem) {
                $amount = isset($expItem['amount']) ? (float) $expItem['amount'] : 0;
                if ($amount <= 0) {
                    continue;
                }

                $title = 'Expense';
                $expTypeId = ! empty($expItem['expense_type_id']) ? $expItem['expense_type_id'] : null;

                if ($expTypeId) {
                    $expType = TerminalExpenseType::find($expTypeId);
                    if ($expType) {
                        $title = $expType->name;
                    }
                } elseif (! empty($expItem['custom_name'])) {
                    $title = trim($expItem['custom_name']);
                }

                $expensesList[] = [
                    'expense_type_id' => $expTypeId,
                    'title' => $title,
                    'amount' => $amount,
                ];
            }
        } else {
            // Legacy fallback
            $legacyFields = [
                'stand_fee' => 'Bus Stand Fee',
                'it_fee' => 'Software / IT Fee',
                'cleaning_fee' => 'Cleaning Fee',
                'staff_allowance' => 'Staff Allowance',
                'other_expense' => 'Other Expense',
            ];

            foreach ($legacyFields as $field => $label) {
                if ($request->filled($field) && (float) $request->$field > 0) {
                    $expensesList[] = [
                        'expense_type_id' => null,
                        'title' => $label,
                        'amount' => (float) $request->$field,
                    ];
                }
            }
        }

        $terminalExpenses = array_sum(array_column($expensesList, 'amount'));
        $driverAllowance = (float) ($request->driver_allowance ?? 0);
        $guardAllowance = (float) ($request->guard_allowance ?? 0);
        $hostessAllowance = (float) ($request->hostess_allowance ?? 0);
        $staffExpenses = $driverAllowance + $guardAllowance + $hostessAllowance;

        $totalExpenses = $terminalExpenses + $staffExpenses;
        $totalRevenue = $bookings->sum('fare_amount');
        $netAmount = $totalRevenue - $totalExpenses;
        $terminalCity = $request->terminal_city ?? ($schedule->route->originCity->name ?? 'Main Terminal');

        // 3. Save Voucher & VoucherExpense records to Database
        $voucherNumber = 'VCH-'.date('Ymd').'-'.strtoupper(Str::random(4));
        $voucher = Voucher::create([
            'voucher_number' => $voucherNumber,
            'schedule_id' => $schedule->id,
            'booking_date' => $request->booking_date,
            'terminal_city' => $terminalCity,
            'total_seats_issued' => $bookings->count(),
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'driver_allowance' => $driverAllowance,
            'guard_allowance' => $guardAllowance,
            'hostess_allowance' => $hostessAllowance,
            'total_staff_expenses' => $staffExpenses,
            'net_amount' => $netAmount,
            'is_final_voucher' => (bool) $request->is_final_voucher,
            'created_by' => auth()->id(),
        ]);

        foreach ($expensesList as $exp) {
            VoucherExpense::create([
                'voucher_id' => $voucher->id,
                'terminal_expense_type_id' => $exp['expense_type_id'],
                'title' => $exp['title'],
                'amount' => $exp['amount'],
            ]);
        }

        $voucher->load(['schedule.route.company', 'schedule.route.originCity', 'schedule.route.destinationCity', 'schedule.vehicleServiceType', 'schedule.vehicle', 'expenses']);

        return view('user.voucher', compact('voucher', 'schedule', 'bookings', 'expensesList', 'totalExpenses', 'totalRevenue', 'terminalCity'));
    }

    /**
     * Get active expense types (AJAX).
     */
    public function getExpenseTypes(): JsonResponse
    {
        $expenseTypes = TerminalExpenseType::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return response()->json($expenseTypes);
    }

    /**
     * Get schedules for a route on a specific date (AJAX).
     */
    public function getSchedules(Request $request): JsonResponse
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'date' => 'required|date',
        ]);

        $schedules = Schedule::with(['vehicleServiceType', 'vehicle', 'route.originCity', 'route.destinationCity'])
            ->where('route_id', $request->route_id)
            ->where('status', 'active')
            ->get()
            ->map(function (Schedule $schedule) use ($request) {
                $bookedSeats = Booking::where('schedule_id', $schedule->id)
                    ->whereDate('booking_date', $request->date)
                    ->where('status', 'confirmed')
                    ->pluck('seat_number')
                    ->toArray();

                $vehicleInfo = $schedule->vehicle
                    ? "Bus {$schedule->vehicle->bus_number} ({$schedule->vehicle->registration_number})"
                    : null;

                return [
                    'id' => $schedule->id,
                    'origin_city_id' => $schedule->route->origin_city_id,
                    'destination_city_id' => $schedule->route->destination_city_id,
                    'departure_time' => $schedule->departure_time,
                    'arrival_time' => $schedule->arrival_time,
                    'duration_minutes' => $schedule->duration_minutes,
                    'vehicle_type' => $schedule->vehicleServiceType->name,
                    'assigned_vehicle' => $vehicleInfo,
                    'total_seats' => $schedule->vehicleServiceType->total_seats,
                    'booked_seats' => $bookedSeats,
                    'available_seats' => $schedule->vehicleServiceType->total_seats - count($bookedSeats),
                ];
            });

        return response()->json($schedules);
    }

    /**
     * Get seat map for a schedule on a specific date (AJAX).
     */
    /**
     * Get adjacent seat numbers for a given seat in 2+2 layout and continuous back row.
     */
    protected function getAdjacentSeatNumbers(int $seatNumber, int $totalSeats): array
    {
        $mainTotal = floor(($totalSeats - 1) / 4) * 4;
        if ($totalSeats >= 41 && $seatNumber > $mainTotal) {
            $adjacents = [];
            if ($seatNumber > $mainTotal + 1) {
                $adjacents[] = $seatNumber - 1;
            }
            if ($seatNumber < $totalSeats) {
                $adjacents[] = $seatNumber + 1;
            }

            return $adjacents;
        }

        $rem = $seatNumber % 4;
        if ($rem === 1) {
            return [$seatNumber + 1];
        } elseif ($rem === 2) {
            return [$seatNumber - 1];
        } elseif ($rem === 3) {
            return [$seatNumber + 1];
        } else {
            return [$seatNumber - 1];
        }
    }

    /**
     * Get seat map for a schedule on a specific date (AJAX).
     */
    public function getSeatMap(Request $request): JsonResponse
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'date' => 'required|date',
        ]);

        $schedule = Schedule::with('vehicleServiceType')->findOrFail($request->schedule_id);

        // Auto-clean expired holds
        SeatHold::where('expires_at', '<', now())->delete();

        // Active holds
        $activeHolds = SeatHold::where('schedule_id', $schedule->id)
            ->whereDate('booking_date', $request->date)
            ->where('expires_at', '>', now())
            ->get()
            ->keyBy('seat_number');

        $bookedSeats = Booking::where('schedule_id', $schedule->id)
            ->whereDate('booking_date', $request->date)
            ->where('status', 'confirmed')
            ->get(['seat_number', 'gender'])
            ->keyBy('seat_number');

        $seats = [];
        $totalSeats = $schedule->vehicleServiceType->total_seats;

        for ($i = 1; $i <= $totalSeats; $i++) {
            $isBooked = $bookedSeats->has($i);
            $hold = $activeHolds->get($i);
            $isHeldByMe = $hold && $hold->user_id === auth()->id();
            $isHeldByOther = $hold && $hold->user_id !== auth()->id();

            // Find adjacent seat gender if adjacent is booked
            $adjacents = $this->getAdjacentSeatNumbers($i, $totalSeats);
            $adjacentGender = null;
            foreach ($adjacents as $adj) {
                if ($bookedSeats->has($adj)) {
                    $adjacentGender = $bookedSeats[$adj]->gender;
                    break;
                }
            }

            $seats[] = [
                'number' => $i,
                'is_booked' => $isBooked,
                'gender' => $isBooked ? $bookedSeats[$i]->gender : null,
                'is_held' => (bool) $hold,
                'is_held_by_me' => $isHeldByMe,
                'is_held_by_other' => $isHeldByOther,
                'adjacent_gender' => $adjacentGender,
            ];
        }

        return response()->json([
            'schedule_id' => $schedule->id,
            'vehicle_type' => $schedule->vehicleServiceType->name,
            'total_seats' => $totalSeats,
            'seats' => $seats,
        ]);
    }

    /**
     * Temporarily hold a seat to prevent double booking across counters.
     */
    public function holdSeat(Request $request): JsonResponse
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'booking_date' => 'required|date',
            'seat_number' => 'required|integer|min:1',
        ]);

        // Clean expired holds
        SeatHold::where('expires_at', '<', now())->delete();

        // Check if already booked
        $isBooked = Booking::where('schedule_id', $request->schedule_id)
            ->whereDate('booking_date', $request->booking_date)
            ->where('seat_number', $request->seat_number)
            ->where('status', 'confirmed')
            ->exists();

        if ($isBooked) {
            return response()->json([
                'success' => false,
                'message' => "Seat #{$request->seat_number} is already confirmed and booked.",
            ], 422);
        }

        // Check if held by another operator
        $existingHold = SeatHold::where('schedule_id', $request->schedule_id)
            ->whereDate('booking_date', $request->booking_date)
            ->where('seat_number', $request->seat_number)
            ->where('expires_at', '>', now())
            ->first();

        if ($existingHold) {
            if ($existingHold->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'held_by_other' => true,
                    'message' => "Seat #{$request->seat_number} is currently held by another counter terminal. Please select another seat.",
                ], 422);
            } else {
                // If held by this user, release (un-hold)
                $existingHold->delete();

                return response()->json([
                    'success' => true,
                    'status' => 'unheld',
                    'seat_number' => $request->seat_number,
                    'message' => "Seat #{$request->seat_number} has been released.",
                ]);
            }
        }

        // Release any other seats previously held by this user for this trip
        SeatHold::where('user_id', auth()->id())
            ->where('schedule_id', $request->schedule_id)
            ->whereDate('booking_date', $request->booking_date)
            ->delete();

        // Create 5-minute temporary hold
        $hold = SeatHold::create([
            'schedule_id' => $request->schedule_id,
            'booking_date' => $request->booking_date,
            'seat_number' => $request->seat_number,
            'user_id' => auth()->id(),
            'expires_at' => now()->addMinutes(5),
        ]);

        return response()->json([
            'success' => true,
            'status' => 'held',
            'seat_number' => $request->seat_number,
            'hold_seconds' => 300,
            'expires_at' => $hold->expires_at->toIso8601String(),
            'message' => "Seat #{$request->seat_number} is temporarily locked for you for 5 minutes.",
        ]);
    }

    /**
     * Release a held seat.
     */
    public function unholdSeat(Request $request): JsonResponse
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'booking_date' => 'required|date',
            'seat_number' => 'required|integer|min:1',
        ]);

        SeatHold::where('schedule_id', $request->schedule_id)
            ->whereDate('booking_date', $request->booking_date)
            ->where('seat_number', $request->seat_number)
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get fare for a city-to-city trip (AJAX with robust fallback).
     */
    public function getFare(Request $request): JsonResponse
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'from_city_id' => 'required|exists:cities,id',
            'to_city_id' => 'required|exists:cities,id',
        ]);

        $schedule = Schedule::with(['route', 'vehicleServiceType'])->findOrFail($request->schedule_id);

        // 1. Try exact fare match for specific city pair + route + service type
        $fare = Fare::where('route_id', $schedule->route_id)
            ->where('from_city_id', $request->from_city_id)
            ->where('to_city_id', $request->to_city_id)
            ->where('vehicle_service_type_id', $schedule->vehicle_service_type_id)
            ->where('status', 'active')
            ->first();

        // 2. Fallback: Search any fare defined on this route for this service type
        if (! $fare) {
            $fare = Fare::where('route_id', $schedule->route_id)
                ->where('vehicle_service_type_id', $schedule->vehicle_service_type_id)
                ->where('status', 'active')
                ->first();
        }

        // 3. Fallback: Search any fare defined for this vehicle service type overall
        if (! $fare) {
            $fare = Fare::where('vehicle_service_type_id', $schedule->vehicle_service_type_id)
                ->where('status', 'active')
                ->first();
        }

        // 4. Ultimate default fallback fare amount if none entered in system
        $fareAmount = $fare ? $fare->fare_amount : 1500;

        return response()->json([
            'fare_amount' => $fareAmount,
            'found' => true,
        ]);
    }

    /**
     * Store a new booking (sell ticket).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'booking_date' => 'required|date',
            'seat_number' => 'required|integer|min:1',
            'from_city_id' => 'required|exists:cities,id',
            'to_city_id' => 'required|exists:cities,id',
            'passenger_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:30',
            'cnic' => 'nullable|string|max:30',
            'gender' => 'required|in:male,female',
            'fare_amount' => 'required|numeric|min:0',
        ]);

        $schedule = Schedule::with('vehicleServiceType')->findOrFail($validated['schedule_id']);

        // 1. Check if seat is already confirmed
        $existingBooking = Booking::where('schedule_id', $validated['schedule_id'])
            ->whereDate('booking_date', $validated['booking_date'])
            ->where('seat_number', $validated['seat_number'])
            ->where('status', 'confirmed')
            ->exists();

        if ($existingBooking) {
            return back()->withInput()->withErrors(['seat_number' => 'This seat is already booked for the selected date.']);
        }

        // 2. Gender Rule Check: Cannot book opposite gender adjacent to an already booked passenger
        $adjacents = $this->getAdjacentSeatNumbers($validated['seat_number'], $schedule->vehicleServiceType->total_seats);
        $conflictingAdjacent = Booking::where('schedule_id', $validated['schedule_id'])
            ->whereDate('booking_date', $validated['booking_date'])
            ->whereIn('seat_number', $adjacents)
            ->where('status', 'confirmed')
            ->where('gender', '!=', $validated['gender'])
            ->first();

        if ($conflictingAdjacent) {
            $oppGender = $conflictingAdjacent->gender === 'female' ? 'Female' : 'Male';
            $reqGender = $conflictingAdjacent->gender === 'female' ? 'Female' : 'Male';

            return back()->withInput()->withErrors([
                'gender' => "Adjacent Seat #{$conflictingAdjacent->seat_number} is already booked by a {$oppGender} passenger. This seat can only be booked for {$reqGender} passengers.",
            ]);
        }

        $validated['booked_by'] = auth()->id();
        $validated['status'] = 'confirmed';

        $booking = Booking::create($validated);

        // Release any temporary hold on this seat
        SeatHold::where('schedule_id', $validated['schedule_id'])
            ->whereDate('booking_date', $validated['booking_date'])
            ->where('seat_number', $validated['seat_number'])
            ->delete();

        return redirect()->route('user.booking.ticket', $booking)
            ->with('success', 'Ticket booked successfully!');
    }

    /**
     * Cancel an existing booking ticket.
     */
    public function cancel(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking #'.$booking->id.' has been cancelled successfully.');
    }

    /**
     * Show printable ticket.
     */
    public function ticket(Booking $booking): View
    {
        $booking->load(['schedule.route.originCity', 'schedule.route.destinationCity', 'schedule.vehicleServiceType', 'schedule.vehicle', 'fromCity', 'toCity', 'bookedByUser']);

        return view('user.bookings.ticket', compact('booking'));
    }
}
