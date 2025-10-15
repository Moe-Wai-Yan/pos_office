<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\ProductSize;
use App\Models\ProductColor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        User::create([
            'name' => 'Admin',
            'email' => 'ecom_4dm!n24@gmail.com',
            'password' => Hash::make('s5gefzuFZ3JyUw=='),
        ]);

        // $sizes = [
        //     ['name' => 'xl'],
        //     ['name' => 'l'],
        //     ['name' => 'm'],
        //     ['name' => 'sm'],
        //     ['name' => 'xxl'],
        // ];

        // $colors = [
        //     [
        //         'english_name' => 'Red',
        //         'myanmar_name' => 'အနီ',
        //     ],
        //     [
        //         'english_name' => 'White',
        //         'myanmar_name' => 'အဖြူ',
        //     ],
        //     [
        //         'english_name' => 'Black',
        //         'myanmar_name' => 'အနက်',
        //     ],
        //     [
        //         'english_name' => 'Blue',
        //         'myanmar_name' => 'အပြာ',
        //     ],
        // ];

        // ProductSize::insert($sizes);

        // ProductColor::insert($colors);
        // $this->call(VersionSeeder::class);
    }
}
