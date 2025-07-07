<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        User::create([
            'name' => 'Admin Toko Bangunan',
            'email' => 'admin@tokobangunan.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '08123456789',
            'address' => 'Jl. Admin No. 1, Jakarta',
        ]);

        // Create Customer User
        User::create([
            'name' => 'Customer Test',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '08987654321',
            'address' => 'Jl. Customer No. 2, Jakarta',
        ]);

        // Create Categories
        $categories = [
            [
                'name' => 'Semen & Beton',
                'description' => 'Semen, beton ready mix, dan material konstruksi dasar',
            ],
            [
                'name' => 'Besi & Baja',
                'description' => 'Besi beton, baja ringan, dan material logam konstruksi',
            ],
            [
                'name' => 'Cat & Finishing',
                'description' => 'Cat tembok, cat kayu, dan material finishing',
            ],
            [
                'name' => 'Keramik & Granit',
                'description' => 'Keramik lantai, granit, dan material penutup lantai',
            ],
            [
                'name' => 'Atap & Penutup',
                'description' => 'Genteng, seng, dan material penutup atap',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Products
        $products = [
            [
                'name' => 'Semen Padang 50kg',
                'description' => 'Semen Portland Composite Cement berkualitas tinggi',
                'price' => 65000,
                'stock' => 100,
                'unit' => 'sak',
                'category_id' => 1,
            ],
            [
                'name' => 'Beton Ready Mix K-250',
                'description' => 'Beton siap pakai dengan mutu K-250',
                'price' => 750000,
                'stock' => 50,
                'unit' => 'm3',
                'category_id' => 1,
            ],
            [
                'name' => 'Besi Beton 12mm',
                'description' => 'Besi beton diameter 12mm panjang 12 meter',
                'price' => 85000,
                'stock' => 200,
                'unit' => 'batang',
                'category_id' => 2,
            ],
            [
                'name' => 'Cat Tembok Dulux 25kg',
                'description' => 'Cat tembok interior dan eksterior warna putih',
                'price' => 450000,
                'stock' => 80,
                'unit' => 'pail',
                'category_id' => 3,
            ],
            [
                'name' => 'Keramik Lantai 40x40',
                'description' => 'Keramik lantai motif marmer ukuran 40x40 cm',
                'price' => 45000,
                'stock' => 300,
                'unit' => 'm2',
                'category_id' => 4,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}