<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['role_id' => 1, 'nama_role' => 'admin'],
            ['role_id' => 2, 'nama_role' => 'penjual'],
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
