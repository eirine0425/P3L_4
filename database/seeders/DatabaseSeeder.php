<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
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
        // Create roles first
        $this->createRoles();
        
        // Create a test user with all required fields
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'dob' => '1990-01-01', // Add required dob field
            'phone_number' => '1234567890', // Add required phone_number field
            'role_id' => 1, // Assuming 1 is admin role
        ]);
    }

    /**
     * Create default roles
     */
    private function createRoles(): void
    {
        $roles = [
            ['role_id' => 1, 'nama_role' => 'admin'],
            ['role_id' => 2, 'nama_role' => 'penitip'],
            ['role_id' => 3, 'nama_role' => 'cs'],
            ['role_id' => 4, 'nama_role' => 'pembeli'],
            ['role_id' => 5, 'nama_role' => 'pegawai'],
            ['role_id' => 6, 'nama_role' => 'kurir'],
            ['role_id' => 7, 'nama_role' => 'organisasi'],
            ['role_id' => 8, 'nama_role' => 'owner'],
            ['role_id' => 9, 'nama_role' => 'hunter'],
            ['role_id' => 10, 'nama_role' => 'gudang'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['role_id' => $role['role_id']],
                ['nama_role' => $role['nama_role']]
            );
        }
    }
}
