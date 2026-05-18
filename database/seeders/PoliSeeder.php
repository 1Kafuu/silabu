<?php

namespace Database\Seeders;

use App\Models\Poli;
use Illuminate\Database\Seeder;

class PoliSeeder extends Seeder
{
    public function run(): void
    {
        $polis = [
            ['name' => 'Poli Umum',  'code' => 'A',  'is_active' => true],
            ['name' => 'Poli Gigi',  'code' => 'G',  'is_active' => true],
            ['name' => 'Poli Anak',  'code' => 'AN', 'is_active' => true],
            ['name' => 'Poli Mata',  'code' => 'M',  'is_active' => true],
            ['name' => 'Poli Bedah', 'code' => 'B',  'is_active' => true],
        ];

        foreach ($polis as $poli) {
            Poli::firstOrCreate(
                ['code' => $poli['code']],
                $poli
            );
        }
    }
}
