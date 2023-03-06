<?php

use Illuminate\Database\Seeder;
use App\Models\Variant;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $variantData = [
            [
                'title' => 'Color',
                'description' => 'Color description',
            ],
            [
                'title' => 'Size',
                'description' => 'Size description',
            ],
            [
                'title' => 'Style',
                'description' => 'Style description',
            ],
        ];

        Variant::insert($variantData);
    }
}
