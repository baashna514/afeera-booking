<?php

use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\FareController;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\TerminalExpenseTypeController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\VehicleServiceTypeController;
use App\Http\Controllers\Owner\CompanyController;
use App\Http\Controllers\User\BookingController;
use App\Models\Booking;
use App\Models\City;
use App\Models\Company;
use App\Models\Fare;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleServiceType;
use Illuminate\Support\Facades\Route as RouteFacade;

// Home Route
RouteFacade::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'owner') {
            return redirect()->route('owner.dashboard');
        } elseif ($role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    return redirect()->route('login');
});

// Direct alias for 'dashboard' route name
RouteFacade::middleware(['auth'])->get('/dashboard', [BookingController::class, 'index'])->name('dashboard');

// 1. Counter User Dashboard & Booking Terminal
RouteFacade::middleware(['auth', 'role:user'])->name('user.')->group(function () {
    RouteFacade::get('/terminal', [BookingController::class, 'index'])->name('dashboard');
    RouteFacade::get('/booking/history', [BookingController::class, 'history'])->name('booking.history');
    RouteFacade::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    RouteFacade::get('/booking/{booking}/ticket', [BookingController::class, 'ticket'])->name('booking.ticket');
    RouteFacade::post('/booking/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
    RouteFacade::post('/terminal/voucher', [BookingController::class, 'generateVoucher'])->name('terminal.voucher');

    // AJAX endpoints
    RouteFacade::post('/api/schedules', [BookingController::class, 'getSchedules'])->name('api.schedules');
    RouteFacade::post('/api/seat-map', [BookingController::class, 'getSeatMap'])->name('api.seatmap');
    RouteFacade::post('/api/fare', [BookingController::class, 'getFare'])->name('api.fare');
    RouteFacade::get('/api/expense-types', [BookingController::class, 'getExpenseTypes'])->name('api.expensetypes');
    RouteFacade::post('/api/seat-hold', [BookingController::class, 'holdSeat'])->name('api.seathold');
    RouteFacade::post('/api/seat-unhold', [BookingController::class, 'unholdSeat'])->name('api.seatunhold');
});

// 2. Platform Super Admin Dashboard & Management
RouteFacade::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    RouteFacade::get('/dashboard', function () {
        $totalUsers = User::count();
        $totalCompanies = Company::count();
        $ownersCount = User::where('role', 'owner')->count();
        $usersCount = User::where('role', 'user')->count();
        $totalCities = City::count();
        $totalRoutes = Route::count();
        $totalSchedules = Schedule::count();
        $totalVehicleTypes = VehicleServiceType::count();
        $totalVehicles = Vehicle::count();
        $totalFares = Fare::count();
        $todayBookings = Booking::whereDate('created_at', today())->count();
        $recentCompanies = Company::with('owner')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalCompanies',
            'ownersCount',
            'usersCount',
            'totalCities',
            'totalRoutes',
            'totalSchedules',
            'totalVehicleTypes',
            'totalVehicles',
            'totalFares',
            'todayBookings',
            'recentCompanies'
        ));
    })->name('dashboard');

    // CRUD Resources
    RouteFacade::resource('cities', CityController::class);
    RouteFacade::resource('vehicle-service-types', VehicleServiceTypeController::class);
    RouteFacade::resource('vehicles', VehicleController::class);
    RouteFacade::resource('routes', RouteController::class);
    RouteFacade::resource('schedules', ScheduleController::class);
    RouteFacade::resource('fares', FareController::class);
    RouteFacade::resource('expense-types', TerminalExpenseTypeController::class);
});

// 3. Owner Dashboard & Company Management
RouteFacade::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    RouteFacade::get('/dashboard', function () {
        $companies = Company::where('owner_id', auth()->id())->latest()->get();
        $totalCompanies = $companies->count();

        return view('owner.dashboard', compact('companies', 'totalCompanies'));
    })->name('dashboard');

    RouteFacade::resource('companies', CompanyController::class);
});

require __DIR__.'/auth.php';
