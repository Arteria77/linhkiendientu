<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // CPU
            ['type' => 'CPU', 'brand' => 'Intel', 'name' => 'Vi xử lý Intel Core i5-13400F', 'socket' => 'LGA 1700', 'specs' => '10 nhân 16 luồng | Up to 4.6GHz | 20MB Cache', 'price' => 4950000, 'img' => 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=400'],
            ['type' => 'CPU', 'brand' => 'Intel', 'name' => 'Vi xử lý Intel Core i7-14700K', 'socket' => 'LGA 1700', 'specs' => '20 nhân 28 luồng | Up to 5.6GHz | 33MB Cache', 'price' => 10200000, 'img' => 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=400'],
            ['type' => 'CPU', 'brand' => 'AMD', 'name' => 'Vi xử lý AMD Ryzen 5 7600X', 'socket' => 'AM5', 'specs' => '6 nhân 12 luồng | Up to 5.3GHz | 32MB Cache', 'price' => 5800000, 'img' => 'https://images.unsplash.com/photo-1555680202-c86f0e12f086?w=400'],

            // RAM
            ['type' => 'RAM', 'brand' => 'Kingston', 'name' => 'RAM Kingston Fury Beast 16GB DDR4 3200MHz', 'socket' => 'DDR4', 'specs' => '16GB (1x16GB) | Bus 3200MHz | Cas 16', 'price' => 950000, 'img' => 'https://images.unsplash.com/photo-1562976540-1502c2145186?w=400'],
            ['type' => 'RAM', 'brand' => 'Corsair', 'name' => 'RAM Corsair Vengeance RGB 32GB DDR5 6000MHz', 'socket' => 'DDR5', 'specs' => '32GB (2x16GB) | Bus 6000MHz | LED RGB', 'price' => 3200000, 'img' => 'https://images.unsplash.com/photo-1562976540-1502c2145186?w=400'],

            // VGA
            ['type' => 'VGA', 'brand' => 'MSI', 'name' => 'Card màn hình MSI RTX 4060 VENTUS 2X 8G', 'socket' => 'PCIe 4.0', 'specs' => '8GB GDDR6 | 128-bit | DLSS 3 | 2 Fans', 'price' => 8200000, 'img' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=400'],
            ['type' => 'VGA', 'brand' => 'Asus', 'name' => 'Card màn hình Asus ROG Strix RTX 4070 Ti Super', 'socket' => 'PCIe 4.0', 'specs' => '16GB GDDR6X | 256-bit | 3 Fans RGB', 'price' => 24500000, 'img' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=400'],

            // Mainboard
            ['type' => 'Mainboard', 'brand' => 'Asus', 'name' => 'Bo mạch chủ Asus TUF GAMING B760M-PLUS', 'socket' => 'LGA 1700', 'specs' => 'Micro-ATX | 4x DDR5 | PCIe 5.0 | M.2 NVMe', 'price' => 3850000, 'img' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=400'],
            
            // SSD
            ['type' => 'SSD', 'brand' => 'Samsung', 'name' => 'Ổ cứng SSD Samsung 980 NVMe M.2 1TB', 'socket' => 'M.2 NVMe', 'specs' => 'Đọc 3500MB/s | Ghi 3000MB/s | PCIe 3.0', 'price' => 2100000, 'img' => 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=400'],
        ];

        $origins = ['Chính hãng (Nhật Bản)', 'Chính hãng (Đài Loan)', 'Chính hãng (Hàn Quốc)'];
        $insertData = [];

        foreach ($products as $item) {
            $insertData[] = [
                'code' => strtoupper(substr($item['type'], 0, 3)) . '-' . rand(1000, 9999),
                'name' => $item['name'],
                'category_type' => $item['type'],
                'brand' => $item['brand'],
                'origin' => $origins[array_rand($origins)],
                'socket_type' => $item['socket'],
                'price' => $item['price'],
                'stock' => rand(0, 30),
                'warranty_months' => rand(1, 3) * 12,
                'condition' => 'Mới 100% Fullbox',
                'image' => $item['img'],
                'gallery' => json_encode([]),
                'specifications' => $item['specs'],
                'description' => '<p>Sản phẩm chính hãng, đầy đủ hóa đơn VAT và tem bảo hành chính hãng từ nhà sản xuất.</p>',
                'is_active' => true,
                'is_featured' => (bool)rand(0, 1),
                'view_count' => rand(20, 900),
                'sold_count' => rand(1, 50),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('categories')->insert($insertData);
    }
}