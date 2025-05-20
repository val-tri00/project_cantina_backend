<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\FoodItem;


class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/meniu.csv');
        if(!file_exists($path)){
            $this->command->error("Fisierul meniu.csv nu a fost gasit!");
            return;
        }

        $file = fopen($path, 'r');

        // prima linie citim
        $header = fgetcsv($file);

        while(($data = fgetcsv($file)) !== false) {
            [$categorie, $nume, $calorii, $pret] = $data;

            // cautam categoria
            $cat = Category::firstOrCreate(['name' => $categorie]);

            // cream slug pt img
            $slug = strtolower(str_replace(' ', '_', $nume));
            $imgPath = "produse/{$slug}.png";

            // adaugam produse
            FoodItem::updateOrCreate(
                ['name' => $nume],
                [
                    'price' => $pret,
                    'calories' => $calorii,
                    'category_id' => $cat->id,
                    'img_path' => $imgPath,
                ]
            );
        }

        fclose($file);

        $this->command->info("Meniu a fost importat cu success din CSV!");
    }

}