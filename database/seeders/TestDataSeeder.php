<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Pembeli;
use App\Models\Pegawai;
use App\Models\Organisasi;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan roles sudah ada
        $this->createRoles();
        
        // Buat test users
        $this->createTestUsers();
    }

    private function createRoles()
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

    private function createTestUsers()
    {
        $users = [
            [
                'name' => 'Admin Test',
                'email' => 'admin@reusemart.com',
                'password' => 'password123',
                'role_id' => 1,
                'dob' => '1990-01-01',
                'phone_number' => '081234567890',
                'type' => 'pegawai'
            ],
            [
                'name' => 'Pembeli Test',
                'email' => 'pembeli@reusemart.com',
                'password' => 'password123',
                'role_id' => 4,
                'dob' => '1995-05-15',
                'phone_number' => '081234567891',
                'type' => 'pembeli'
            ],
            [
                'name' => 'Gudang Test',
                'email' => 'gudang@reusemart.com',
                'password' => 'password123',
                'role_id' => 10,
                'dob' => '1988-03-20',
                'phone_number' => '081234567892',
                'type' => 'pegawai'
            ],
            [
                'name' => 'CS Test',
                'email' => 'cs@reusemart.com',
                'password' => 'password123',
                'role_id' => 3,
                'dob' => '1992-07-10',
                'phone_number' => '081234567893',
                'type' => 'pegawai'
            ],
            [
                'name' => 'Organisasi Test',
                'email' => 'organisasi@reusemart.com',
                'password' => 'password123',
                'role_id' => 7,
                'dob' => '1985-12-25',
                'phone_number' => '081234567894',
                'type' => 'organisasi'
            ],
            [
                'name' => 'Gudang User',
                'email' => 'gudang1@gmail.com',
                'password' => 'password123',
                'role_id' => 10,
                'dob' => '1990-01-01',
                'phone_number' => '081234567895',
                'type' => 'pegawai'
            ]
        ];

        foreach ($users as $userData) {
            // Cek apakah user sudah ada
            $existingUser = User::where('email', $userData['email'])->first();
            if ($existingUser) {
                $this->command->info("User {$userData['email']} already exists, skipping...");
                continue;
            }

            // Buat user baru
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role_id' => $userData['role_id'],
                'dob' => $userData['dob'],
                'phone_number' => $userData['phone_number'],
            ]);

            // Buat record terkait berdasarkan tipe
            switch ($userData['type']) {
                case 'pembeli':
                    Pembeli::create([
                        'user_id' => $user->id,
                        'nama' => $user->name,
                        'poin_loyalitas' => 0,
                        'tanggal_registrasi' => now()
                    ]);
                    break;

                case 'pegawai':
                    $jabatanNames = [
                        1 => 'Admin',
                        3 => 'Customer Service',
                        6 => 'Kurir',
                        8 => 'Owner',
                        9 => 'Hunter',
                        10 => 'Gudang'
                    ];
                    
                    Pegawai::create([
                        'user_id' => $user->id,
                        'nama' => $user->name,
                        'nama_jabatan' => $jabatanNames[$userData['role_id']] ?? 'Staff',
                        'tanggal_bergabung' => now(),
                        'nominal_komisi' => 0,
                        'status_aktif' => 'Aktif',
                    ]);
                    break;

                case 'organisasi':
                    Organisasi::create([
                        'user_id' => $user->id,
                        'nama_organisasi' => $user->name,
                        'alamat' => 'Alamat Test Organisasi',
                        'deskripsi' => 'Deskripsi test organisasi',
                    ]);
                    break;
            }

            $this->command->info("Created user: {$userData['email']} with role: {$userData['role_id']}");
        }
    }
}

