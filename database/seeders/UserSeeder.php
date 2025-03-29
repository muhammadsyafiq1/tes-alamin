<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'uuid' => Uuid::uuid4()->toString(),
            'username' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('rahasia'),
            'status' => 'admin',
        ]);

        User::create([
            'uuid' => Uuid::uuid4()->toString(),
            'username' => 'BPRS',
            'email' => 'bprs@admin.com',
            'password' => Hash::make('rahasia'),
            'status' => 'BPRS',
        ]);
    }
}
