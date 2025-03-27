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
        $meniu = [
            'Supe si ciorbe' => [
                'Ciorba burta', 'Ciorba de cartofi', 'Ciorba de salata'
            ],
            'Feluri principale' => [
                'Pizza', 'Chiftele porc', 'Escalop cu ciuperci'
            ],
            'Garnituri' => [
                'Piure de cartofi', 'Cartofi pai'
            ],
            'Salate' => [
                'Salata orientala', 'Salata varza'
            ],
            'Deserturi' => [
                'Ecler', 'Tiramisu'
            ],
            'Produse de panificatie' => [
                'Chifla traditionala'
            ],
            'Bauturi' => [
                'cafea espresso'
            ],
            'Sosuri' => [
                'Smantana', 'Sos de usturoi'
            ]
        ];

        foreach ($meniu as $categorie => $produse){
            $cat = Category::create(['name' => $categorie]);

            foreach ($produse as $produs){
                FoodItem::create([
                    'name' => $produs,
                    'category_id' => $cat->id,
                    'price' => rand(5, 30)
                ]);
            }
        }
    }
}
