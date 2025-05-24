<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Platform;
class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            ['name' => 'Twitter', 'type' => 'twitter'],
            ['name' => 'Instagram', 'type' => 'instagram'],
            ['name' => 'LinkedIn', 'type' => 'linkedin'],
        ];

        foreach ($platforms as $platform) {
            Platform::firstOrCreate(['type' => $platform['type']], $platform);
        }
    }
}