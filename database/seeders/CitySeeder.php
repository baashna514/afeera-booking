<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            // Punjab
            ['name' => 'Lahore', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Faisalabad', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rawalpindi', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Multan', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gujranwala', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sialkot', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bahawalpur', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sargodha', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sahiwal', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gujrat', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jhelum', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dera Ghazi Khan', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Layyah', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mianwali', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jhang', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Khanewal', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rahim Yar Khan', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vehari', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Okara', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kasur', 'province' => 'Punjab', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],

            // Sindh
            ['name' => 'Karachi', 'province' => 'Sindh', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hyderabad', 'province' => 'Sindh', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sukkur', 'province' => 'Sindh', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Larkana', 'province' => 'Sindh', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nawabshah', 'province' => 'Sindh', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mirpur Khas', 'province' => 'Sindh', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dadu', 'province' => 'Sindh', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jacobabad', 'province' => 'Sindh', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Khairpur', 'province' => 'Sindh', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sanghar', 'province' => 'Sindh', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shikarpur', 'province' => 'Sindh', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ghotki', 'province' => 'Sindh', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],

            // KPK
            ['name' => 'Peshawar', 'province' => 'KPK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mardan', 'province' => 'KPK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mingora (Swat)', 'province' => 'KPK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Abbottabad', 'province' => 'KPK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kohat', 'province' => 'KPK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bannu', 'province' => 'KPK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dera Ismail Khan', 'province' => 'KPK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Charsadda', 'province' => 'KPK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nowshera', 'province' => 'KPK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Haripur', 'province' => 'KPK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Swabi', 'province' => 'KPK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mansehra', 'province' => 'KPK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],

            // Balochistan
            ['name' => 'Quetta', 'province' => 'Balochistan', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gwadar', 'province' => 'Balochistan', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Turbat', 'province' => 'Balochistan', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Khuzdar', 'province' => 'Balochistan', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chaman', 'province' => 'Balochistan', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Zhob', 'province' => 'Balochistan', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Loralai', 'province' => 'Balochistan', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sibi', 'province' => 'Balochistan', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],

            // Islamabad & Territories
            ['name' => 'Islamabad', 'province' => 'ICT', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Muzaffarabad', 'province' => 'AJK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mirpur', 'province' => 'AJK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kotli', 'province' => 'AJK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rawalakot', 'province' => 'AJK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gilgit', 'province' => 'GB', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Skardu', 'province' => 'GB', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hunza', 'province' => 'GB', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($cities as $cityData) {
            DB::table('cities')->updateOrInsert(
                ['name' => $cityData['name']],
                $cityData
            );
        }
    }
}
