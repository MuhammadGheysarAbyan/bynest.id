<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['[Contoh] Website Company Profile', 'contoh-website-company-profile', 'Website Development'],
            ['[Contoh] Desain Aplikasi Mobile', 'contoh-desain-aplikasi-mobile', 'UI/UX Design'],
            ['[Contoh] Template Presentasi Bisnis', 'contoh-template-presentasi-bisnis', 'PowerPoint Editing'],
        ];

        foreach ($items as [$title, $slug, $category]) {
            Portfolio::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'category' => $category,
                    'description' => 'Ini adalah data contoh sementara. Ganti dengan project nyata Bynest.id.',
                ]
            );
        }
    }
}