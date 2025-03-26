<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Workspace;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        //Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $clientRole = Role::firstOrCreate(['name' => 'client']);
        
        //Usuario administrador
        $adminUser = User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $adminUser->assignRole($adminRole); 

        //Usuario cliente
        $clientUser = User::create([
            'name' => 'Cliente',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
        ]);
        $clientUser->assignRole($clientRole);

        $workspaces = Workspace::factory(20)->create();

        Reservation::factory(100)->create([
            'user_id' => $clientUser->id,
            'workspace_id' => $workspaces->random()->id,
        ]);

        Reservation::factory(50)->create([
            'user_id' => $clientUser->id,
            'workspace_id' => 1,
        ]);
    }
}
