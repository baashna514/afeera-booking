<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Company;
use App\Models\Fare;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleServiceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run CitySeeder
        $this->call(CitySeeder::class);

        // 1. Create Demo Users for all 3 Roles
        $admin = User::firstOrCreate(
            ['email' => 'admin@ticketing.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
            ]
        );

        $owner = User::firstOrCreate(
            ['email' => 'owner@ticketing.com'],
            [
                'name' => 'Bus Company Owner',
                'password' => Hash::make('password123'),
                'role' => 'owner',
            ]
        );

        $counterUser = User::firstOrCreate(
            ['email' => 'user@ticketing.com'],
            [
                'name' => 'Counter Operator',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );

        // 2. Create Companies
        $faisalMovers = Company::firstOrCreate(
            ['code' => 'FM-01'],
            [
                'owner_id' => $owner->id,
                'name' => 'Faisal Movers Express',
                'phone' => '0300-1234567',
                'address' => 'Main Bus Terminal, Lahore',
                'status' => 'active',
            ]
        );

        $daewoo = Company::firstOrCreate(
            ['code' => 'DW-02'],
            [
                'owner_id' => $owner->id,
                'name' => 'Daewoo Express Bus Service',
                'phone' => '0300-7654321',
                'address' => 'Kalma Chowk, Lahore',
                'status' => 'active',
            ]
        );

        // 3. Create Cities
        $layyah = City::firstOrCreate(['name' => 'Layyah'], ['province' => 'Punjab', 'status' => 'active']);
        $lahore = City::firstOrCreate(['name' => 'Lahore'], ['province' => 'Punjab', 'status' => 'active']);
        $shorkot = City::firstOrCreate(['name' => 'ShorKot'], ['province' => 'Punjab', 'status' => 'active']);
        $chowkMunda = City::firstOrCreate(['name' => 'Chowk Munda'], ['province' => 'Punjab', 'status' => 'active']);
        $multan = City::firstOrCreate(['name' => 'Multan'], ['province' => 'Punjab', 'status' => 'active']);

        // 4. Create Vehicle Service Types
        $goldClass = VehicleServiceType::firstOrCreate(
            ['name' => 'Gold Class'],
            ['total_seats' => 18, 'description' => 'VIP luxury seating with 18 seats, extra legroom & meals.', 'status' => 'active']
        );

        $businessClass = VehicleServiceType::firstOrCreate(
            ['name' => 'Business Class'],
            ['total_seats' => 37, 'description' => 'Executive 37 seats layout with AC & entertainment.', 'status' => 'active']
        );

        $economyClass = VehicleServiceType::firstOrCreate(
            ['name' => 'Economy Class'],
            ['total_seats' => 45, 'description' => 'Standard 45 seats comfortable travel.', 'status' => 'active']
        );

        // 5. Create Bus Fleet Vehicles
        $bus1 = Vehicle::firstOrCreate(
            ['bus_number' => 'B-101'],
            [
                'registration_number' => 'LHR-8899',
                'company_id' => $faisalMovers->id,
                'vehicle_service_type_id' => $goldClass->id,
                'status' => 'active',
            ]
        );

        $bus2 = Vehicle::firstOrCreate(
            ['bus_number' => 'B-102'],
            [
                'registration_number' => 'MN-4455',
                'company_id' => $faisalMovers->id,
                'vehicle_service_type_id' => $businessClass->id,
                'status' => 'active',
            ]
        );

        $bus3 = Vehicle::firstOrCreate(
            ['bus_number' => 'B-201'],
            [
                'registration_number' => 'LE-1122',
                'company_id' => $daewoo->id,
                'vehicle_service_type_id' => $economyClass->id,
                'status' => 'active',
            ]
        );

        // 6. Create Routes with intermediate stops
        $routeMotorway = Route::firstOrCreate(
            ['name' => 'Layyah to Lahore via Motorway'],
            [
                'company_id' => $faisalMovers->id,
                'origin_city_id' => $layyah->id,
                'destination_city_id' => $lahore->id,
                'status' => 'active',
            ]
        );

        // Stops for Motorway route
        $routeMotorway->stops()->delete();
        $routeMotorway->stops()->create(['city_id' => $chowkMunda->id, 'stop_order' => 1, 'distance_from_origin_km' => 45, 'duration_from_origin_minutes' => 40]);
        $routeMotorway->stops()->create(['city_id' => $shorkot->id, 'stop_order' => 2, 'distance_from_origin_km' => 110, 'duration_from_origin_minutes' => 100]);

        $routeGTRoad = Route::firstOrCreate(
            ['name' => 'Layyah to Lahore via GT Road'],
            [
                'company_id' => $daewoo->id,
                'origin_city_id' => $layyah->id,
                'destination_city_id' => $lahore->id,
                'status' => 'active',
            ]
        );

        // 7. Create Schedules (Departure times & assigned bus fleet)
        $sch1 = Schedule::firstOrCreate(
            ['route_id' => $routeMotorway->id, 'departure_time' => '08:00:00'],
            [
                'vehicle_service_type_id' => $economyClass->id,
                'vehicle_id' => $bus3->id,
                'duration_minutes' => 330,
                'arrival_time' => '13:30:00',
                'status' => 'active',
            ]
        );

        $sch2 = Schedule::firstOrCreate(
            ['route_id' => $routeMotorway->id, 'departure_time' => '10:00:00'],
            [
                'vehicle_service_type_id' => $goldClass->id,
                'vehicle_id' => $bus1->id,
                'duration_minutes' => 300,
                'arrival_time' => '15:00:00',
                'status' => 'active',
            ]
        );

        $sch3 = Schedule::firstOrCreate(
            ['route_id' => $routeMotorway->id, 'departure_time' => '11:00:00'],
            [
                'vehicle_service_type_id' => $businessClass->id,
                'vehicle_id' => $bus2->id,
                'duration_minutes' => 330,
                'arrival_time' => '16:30:00',
                'status' => 'active',
            ]
        );

        // 8. Create Fares
        Fare::firstOrCreate([
            'route_id' => $routeMotorway->id,
            'from_city_id' => $layyah->id,
            'to_city_id' => $lahore->id,
            'vehicle_service_type_id' => $economyClass->id,
        ], ['fare_amount' => 1500, 'status' => 'active']);

        Fare::firstOrCreate([
            'route_id' => $routeMotorway->id,
            'from_city_id' => $layyah->id,
            'to_city_id' => $lahore->id,
            'vehicle_service_type_id' => $goldClass->id,
        ], ['fare_amount' => 2500, 'status' => 'active']);

        Fare::firstOrCreate([
            'route_id' => $routeMotorway->id,
            'from_city_id' => $layyah->id,
            'to_city_id' => $lahore->id,
            'vehicle_service_type_id' => $businessClass->id,
        ], ['fare_amount' => 1900, 'status' => 'active']);

        Fare::firstOrCreate([
            'route_id' => $routeMotorway->id,
            'from_city_id' => $shorkot->id,
            'to_city_id' => $lahore->id,
            'vehicle_service_type_id' => $economyClass->id,
        ], ['fare_amount' => 900, 'status' => 'active']);

        // 9. Seed Default Terminal Expense Types
        $defaultExpenseTypes = [
            'Bus Stand Fee',
            'Software / IT Fee',
            'Terminal Cleaning Fee',
            'Staff Allowance',
            'Commission',
            'Generator / Fuel Expense',
            'Tea & Refreshment',
        ];

        foreach ($defaultExpenseTypes as $expName) {
            \App\Models\TerminalExpenseType::firstOrCreate(
                ['name' => $expName],
                ['status' => 'active']
            );
        }
    }
}
