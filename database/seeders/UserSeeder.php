<?php

namespace Database\Seeders;

use App\Modules\Users\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'teste@example.com'],
            [
                'name' => 'Usuário Teste',
                'password' => Hash::make('senha123'),
            ]
        );
    }
}
