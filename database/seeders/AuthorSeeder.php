<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            ['name' => 'mohamed nabil'],
            ['name' => 'ahmed samir'],
            ['name' => 'sara ali'],

        ];
        Author::insert($authors);
    }
}
