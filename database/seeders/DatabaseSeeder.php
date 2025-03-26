<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Workspace;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user = User::create([
            'name' => 'Usuario de prueba',
            'email' => 'ejemplo@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $workspaces = Workspace::factory(20)->create();

        Reservation::factory(100)->create([
            'user_id' => $user->id,
            'workspace_id' => $workspaces->random()->id, 
        ]);

        Reservation::factory(50)->create([
            'user_id' => $user->id,
            'workspace_id' => 1, 
        ]);
    }
}
