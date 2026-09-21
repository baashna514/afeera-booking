<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\City;
use App\Models\Company;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\SeatHold;
use App\Models\TerminalExpenseType;
use App\Models\User;
use App\Models\VehicleServiceType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketingTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Online');
    }

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign In to Your Account');
    }

    public function test_register_page_loads(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Create your account to access the system');
    }

    public function test_user_can_register(): void
    {
        $email = 'newuser_'.time().'@test.com';

        $response = $this->post('/register', [
            'name' => 'Test Staff',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'user',
        ]);
    }

    public function test_owner_login_redirects_to_owner_dashboard(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->get('/owner/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Platform Owner Portal');
    }

    public function test_owner_can_create_company(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->post(route('owner.companies.store'), [
            'name' => 'Al-Makkah Coach',
            'code' => 'AMC-99',
            'phone' => '+92 300 1122334',
            'address' => 'General Bus Stand, Faisalabad',
        ]);

        $response->assertRedirect(route('owner.companies.index'));
        $this->assertDatabaseHas('companies', [
            'name' => 'Al-Makkah Coach',
            'code' => 'AMC-99',
            'owner_id' => $owner->id,
        ]);
    }

    public function test_super_admin_can_manage_cities(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        // Create city
        $response = $this->actingAs($admin)->post(route('admin.cities.store'), [
            'name' => 'Layyah',
            'province' => 'Punjab',
            'status' => 'active',
        ]);
        $response->assertRedirect(route('admin.cities.index'));
        $this->assertDatabaseHas('cities', ['name' => 'Layyah']);

        $city = City::first();

        // Update city
        $response = $this->actingAs($admin)->put(route('admin.cities.update', $city), [
            'name' => 'Layyah City Updated',
            'province' => 'Punjab',
            'status' => 'active',
        ]);
        $response->assertRedirect(route('admin.cities.index'));
        $this->assertDatabaseHas('cities', ['name' => 'Layyah City Updated']);
    }

    public function test_super_admin_can_manage_vehicle_service_types(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($admin)->post(route('admin.vehicle-service-types.store'), [
            'name' => 'Gold Class',
            'total_seats' => 18,
            'description' => 'Luxury VIP 18 seats',
            'status' => 'active',
        ]);
        $response->assertRedirect(route('admin.vehicle-service-types.index'));
        $this->assertDatabaseHas('vehicle_service_types', [
            'name' => 'Gold Class',
            'total_seats' => 18,
        ]);
    }

    public function test_super_admin_can_manage_vehicles(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $owner = User::factory()->create(['role' => 'owner']);
        $company = Company::create(['owner_id' => $owner->id, 'name' => 'Test Bus Co', 'status' => 'active']);
        $vType = VehicleServiceType::create(['name' => 'Gold Class', 'total_seats' => 18, 'status' => 'active']);

        $response = $this->actingAs($admin)->post(route('admin.vehicles.store'), [
            'bus_number' => 'B-102',
            'registration_number' => 'ABC-1234',
            'company_id' => $company->id,
            'vehicle_service_type_id' => $vType->id,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.vehicles.index'));
        $this->assertDatabaseHas('vehicles', [
            'bus_number' => 'B-102',
            'registration_number' => 'ABC-1234',
        ]);
    }

    public function test_super_admin_can_manage_routes(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $owner = User::factory()->create(['role' => 'owner']);
        $company = Company::create(['owner_id' => $owner->id, 'name' => 'Test Bus Co', 'status' => 'active']);
        $origin = City::create(['name' => 'Layyah', 'status' => 'active']);
        $dest = City::create(['name' => 'Lahore', 'status' => 'active']);

        $response = $this->actingAs($admin)->post(route('admin.routes.store'), [
            'name' => 'Layyah to Lahore via Motorway',
            'company_id' => $company->id,
            'origin_city_id' => $origin->id,
            'destination_city_id' => $dest->id,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.routes.index'));
        $this->assertDatabaseHas('routes', [
            'name' => 'Layyah to Lahore via Motorway',
        ]);
    }

    public function test_counter_operator_can_issue_and_cancel_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $owner = User::factory()->create(['role' => 'owner']);
        $company = Company::create(['owner_id' => $owner->id, 'name' => 'Test Bus Co', 'status' => 'active']);
        $origin = City::create(['name' => 'Layyah', 'status' => 'active']);
        $dest = City::create(['name' => 'Lahore', 'status' => 'active']);
        $vType = VehicleServiceType::create(['name' => 'Gold Class', 'total_seats' => 18, 'status' => 'active']);
        $route = Route::create([
            'name' => 'Layyah to Lahore',
            'company_id' => $company->id,
            'origin_city_id' => $origin->id,
            'destination_city_id' => $dest->id,
            'status' => 'active',
        ]);
        $schedule = Schedule::create([
            'route_id' => $route->id,
            'vehicle_service_type_id' => $vType->id,
            'departure_time' => '10:00:00',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post(route('user.booking.store'), [
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'seat_number' => 5,
            'from_city_id' => $origin->id,
            'to_city_id' => $dest->id,
            'passenger_name' => 'Ali Ahmad',
            'gender' => 'male',
            'fare_amount' => 2500,
        ]);

        $this->assertDatabaseHas('bookings', [
            'schedule_id' => $schedule->id,
            'seat_number' => 5,
            'passenger_name' => 'Ali Ahmad',
            'gender' => 'male',
            'fare_amount' => 2500,
            'booked_by' => $user->id,
        ]);

        $booking = Booking::first();

        // Check Counter User ticket history page
        $historyRes = $this->actingAs($user)->get(route('user.booking.history'));
        $historyRes->assertStatus(200);
        $historyRes->assertSee('Ali Ahmad');

        // Cancel booking
        $cancelRes = $this->actingAs($user)->post(route('user.booking.cancel', $booking));
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_super_admin_can_manage_terminal_expense_types(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($admin)->post(route('admin.expense-types.store'), [
            'name' => 'Generator Fuel Expense',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.expense-types.index'));
        $this->assertDatabaseHas('terminal_expense_types', [
            'name' => 'Generator Fuel Expense',
            'status' => 'active',
        ]);
    }

    public function test_counter_operator_can_generate_voucher_with_dynamic_expenses(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $owner = User::factory()->create(['role' => 'owner']);
        $company = Company::create(['owner_id' => $owner->id, 'name' => 'Test Bus Co', 'status' => 'active']);
        $origin = City::create(['name' => 'Layyah', 'status' => 'active']);
        $dest = City::create(['name' => 'Lahore', 'status' => 'active']);
        $vType = VehicleServiceType::create(['name' => 'Economy Class', 'total_seats' => 45, 'status' => 'active']);
        $route = Route::create([
            'name' => 'Layyah to Lahore',
            'company_id' => $company->id,
            'origin_city_id' => $origin->id,
            'destination_city_id' => $dest->id,
            'status' => 'active',
        ]);
        $schedule = Schedule::create([
            'route_id' => $route->id,
            'vehicle_service_type_id' => $vType->id,
            'departure_time' => '10:00:00',
            'status' => 'active',
        ]);

        $expType1 = TerminalExpenseType::create(['name' => 'Bus Stand Fee', 'status' => 'active']);
        $expType2 = TerminalExpenseType::create(['name' => 'Software Fee', 'status' => 'active']);

        $response = $this->actingAs($user)->post(route('user.terminal.voucher'), [
            'schedule_id' => $schedule->id,
            'booking_date' => date('Y-m-d'),
            'terminal_city' => 'Layyah Main Station',
            'expenses' => [
                ['expense_type_id' => $expType1->id, 'amount' => 500],
                ['expense_type_id' => $expType2->id, 'amount' => 200],
                ['custom_name' => 'Staff Tea & Snacks', 'amount' => 150],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertSee('Bus Stand Fee');
        $response->assertSee('Software Fee');
        $response->assertSee('Staff Tea & Snacks');
        $response->assertSee('850');

        $this->assertDatabaseHas('vouchers', [
            'schedule_id' => $schedule->id,
            'terminal_city' => 'Layyah Main Station',
            'total_expenses' => 850,
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('voucher_expenses', [
            'title' => 'Bus Stand Fee',
            'amount' => 500,
        ]);
        $this->assertDatabaseHas('voucher_expenses', [
            'title' => 'Software Fee',
            'amount' => 200,
        ]);
    }

    public function test_owner_and_super_admin_can_register_and_redirect_correctly(): void
    {
        // 1. Owner registration
        $ownerEmail = 'owner_'.time().'@test.com';
        $resOwner = $this->post('/register', [
            'name' => 'Test Owner',
            'email' => $ownerEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'owner',
        ]);
        $resOwner->assertRedirect(route('owner.dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => $ownerEmail,
            'role' => 'owner',
        ]);

        auth()->logout();

        // 2. Super Admin registration
        $adminEmail = 'admin_'.time().'@test.com';
        $resAdmin = $this->post('/register', [
            'name' => 'Test Super Admin',
            'email' => $adminEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'super_admin',
        ]);
        $resAdmin->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => $adminEmail,
            'role' => 'super_admin',
        ]);
    }

    public function test_seat_hold_prevents_concurrent_booking_from_other_terminals(): void
    {
        $operator1 = User::factory()->create(['role' => 'user']);
        $operator2 = User::factory()->create(['role' => 'user']);

        $owner = User::factory()->create(['role' => 'owner']);
        $company = Company::create(['owner_id' => $owner->id, 'name' => 'Express Coach', 'status' => 'active']);
        $origin = City::create(['name' => 'Lahore', 'status' => 'active']);
        $dest = City::create(['name' => 'Islamabad', 'status' => 'active']);
        $vType = VehicleServiceType::create(['name' => 'Executive', 'total_seats' => 40, 'status' => 'active']);
        $route = Route::create([
            'name' => 'Lahore to Islamabad',
            'company_id' => $company->id,
            'origin_city_id' => $origin->id,
            'destination_city_id' => $dest->id,
            'status' => 'active',
        ]);
        $schedule = Schedule::create([
            'route_id' => $route->id,
            'vehicle_service_type_id' => $vType->id,
            'departure_time' => '09:00:00',
            'status' => 'active',
        ]);

        $travelDate = date('Y-m-d');

        // Operator 1 holds Seat #7
        $holdRes = $this->actingAs($operator1)->postJson(route('user.api.seathold'), [
            'schedule_id' => $schedule->id,
            'booking_date' => $travelDate,
            'seat_number' => 7,
        ]);
        $holdRes->assertStatus(200);
        $holdRes->assertJson([
            'success' => true,
            'status' => 'held',
            'seat_number' => 7,
        ]);

        $this->assertDatabaseHas('seat_holds', [
            'schedule_id' => $schedule->id,
            'seat_number' => 7,
            'user_id' => $operator1->id,
        ]);

        // Operator 2 attempts to hold Seat #7 -> Rejected with 422
        $conflictRes = $this->actingAs($operator2)->postJson(route('user.api.seathold'), [
            'schedule_id' => $schedule->id,
            'booking_date' => $travelDate,
            'seat_number' => 7,
        ]);
        $conflictRes->assertStatus(422);
        $conflictRes->assertJson([
            'success' => false,
            'held_by_other' => true,
        ]);

        // Operator 1 releases Seat #7
        $unholdRes = $this->actingAs($operator1)->postJson(route('user.api.seatunhold'), [
            'schedule_id' => $schedule->id,
            'booking_date' => $travelDate,
            'seat_number' => 7,
        ]);
        $unholdRes->assertStatus(200);
        $this->assertDatabaseMissing('seat_holds', [
            'schedule_id' => $schedule->id,
            'seat_number' => 7,
        ]);

        // Now Operator 2 can hold Seat #7
        $op2HoldRes = $this->actingAs($operator2)->postJson(route('user.api.seathold'), [
            'schedule_id' => $schedule->id,
            'booking_date' => $travelDate,
            'seat_number' => 7,
        ]);
        $op2HoldRes->assertStatus(200);
        $op2HoldRes->assertJson(['status' => 'held']);
    }

    public function test_adjacent_seat_gender_policy_enforcement(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $owner = User::factory()->create(['role' => 'owner']);
        $company = Company::create(['owner_id' => $owner->id, 'name' => 'Express Coach', 'status' => 'active']);
        $origin = City::create(['name' => 'Layyah', 'status' => 'active']);
        $dest = City::create(['name' => 'Rawalpindi', 'status' => 'active']);
        $vType = VehicleServiceType::create(['name' => 'Luxury', 'total_seats' => 45, 'status' => 'active']);
        $route = Route::create([
            'name' => 'Layyah to Rawalpindi',
            'company_id' => $company->id,
            'origin_city_id' => $origin->id,
            'destination_city_id' => $dest->id,
            'status' => 'active',
        ]);
        $schedule = Schedule::create([
            'route_id' => $route->id,
            'vehicle_service_type_id' => $vType->id,
            'departure_time' => '11:00:00',
            'status' => 'active',
        ]);

        $travelDate = date('Y-m-d');

        // Book Seat 1 (Male passenger)
        $this->actingAs($user)->post(route('user.booking.store'), [
            'schedule_id' => $schedule->id,
            'booking_date' => $travelDate,
            'seat_number' => 1,
            'from_city_id' => $origin->id,
            'to_city_id' => $dest->id,
            'passenger_name' => 'Usman Tariq',
            'phone_number' => '03001234567',
            'gender' => 'male',
            'fare_amount' => 2000,
        ]);

        $this->assertDatabaseHas('bookings', [
            'seat_number' => 1,
            'gender' => 'male',
            'status' => 'confirmed',
        ]);

        // Attempt to book Seat 2 (adjacent to Seat 1) for a Female passenger -> must be rejected
        $oppGenderRes = $this->actingAs($user)->from(route('user.dashboard'))->post(route('user.booking.store'), [
            'schedule_id' => $schedule->id,
            'booking_date' => $travelDate,
            'seat_number' => 2,
            'from_city_id' => $origin->id,
            'to_city_id' => $dest->id,
            'passenger_name' => 'Fatima Bibi',
            'phone_number' => '03009876543',
            'gender' => 'female',
            'fare_amount' => 2000,
        ]);

        $oppGenderRes->assertSessionHasErrors('gender');
        $this->assertDatabaseMissing('bookings', [
            'seat_number' => 2,
            'passenger_name' => 'Fatima Bibi',
        ]);

        // Booking Seat 2 for a Male passenger -> succeeds
        $sameGenderRes = $this->actingAs($user)->post(route('user.booking.store'), [
            'schedule_id' => $schedule->id,
            'booking_date' => $travelDate,
            'seat_number' => 2,
            'from_city_id' => $origin->id,
            'to_city_id' => $dest->id,
            'passenger_name' => 'Bilal Khan',
            'phone_number' => '03009876543',
            'gender' => 'male',
            'fare_amount' => 2000,
        ]);

        $sameGenderRes->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bookings', [
            'seat_number' => 2,
            'passenger_name' => 'Bilal Khan',
            'gender' => 'male',
            'status' => 'confirmed',
        ]);
    }

    public function test_seat_hold_is_released_upon_successful_booking(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $owner = User::factory()->create(['role' => 'owner']);
        $company = Company::create(['owner_id' => $owner->id, 'name' => 'Express Coach', 'status' => 'active']);
        $origin = City::create(['name' => 'Lahore', 'status' => 'active']);
        $dest = City::create(['name' => 'Multan', 'status' => 'active']);
        $vType = VehicleServiceType::create(['name' => 'Standard', 'total_seats' => 30, 'status' => 'active']);
        $route = Route::create([
            'name' => 'Lahore to Multan',
            'company_id' => $company->id,
            'origin_city_id' => $origin->id,
            'destination_city_id' => $dest->id,
            'status' => 'active',
        ]);
        $schedule = Schedule::create([
            'route_id' => $route->id,
            'vehicle_service_type_id' => $vType->id,
            'departure_time' => '14:00:00',
            'status' => 'active',
        ]);

        $travelDate = date('Y-m-d');

        // Create hold on Seat 10
        SeatHold::create([
            'schedule_id' => $schedule->id,
            'booking_date' => $travelDate,
            'seat_number' => 10,
            'user_id' => $user->id,
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->assertDatabaseHas('seat_holds', [
            'schedule_id' => $schedule->id,
            'seat_number' => 10,
        ]);

        // Complete booking for Seat 10
        $this->actingAs($user)->post(route('user.booking.store'), [
            'schedule_id' => $schedule->id,
            'booking_date' => $travelDate,
            'seat_number' => 10,
            'from_city_id' => $origin->id,
            'to_city_id' => $dest->id,
            'passenger_name' => 'Hamza Ali',
            'gender' => 'male',
            'fare_amount' => 1800,
        ]);

        // Seat hold should be deleted
        $this->assertDatabaseMissing('seat_holds', [
            'schedule_id' => $schedule->id,
            'seat_number' => 10,
        ]);
    }
}
