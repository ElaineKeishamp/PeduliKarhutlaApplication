<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Logistik' => 'logistik',
            'Kesehatan' => 'kesehatan',
            'Pemadaman' => 'pemadaman',
            'Pemulihan Lahan' => 'pemulihan-lahan',
            'Evakuasi' => 'evakuasi',
            'Perlindungan Satwa' => 'perlindungan-satwa',
        ] as $name => $slug) {
            Category::updateOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }
}
