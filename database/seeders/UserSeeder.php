<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'dev@localhost.com', 'username' => 'devadmin'],
            [
                'name' => 'Development Admin',
                'password' => bcrypt('password'),
                'is_active' => true,
                'can_login' => true,
                'notify_petty_cash_request' => true,
                'notify_petty_cash_verify' => true,
                'notify_petty_cash_approve' => true,
                'notify_petty_cash_payment' => true,
            ]
        );

        $user->assignRole('Super Admin');

        $this->command->info('Development admin user created!');
        $this->command->info('Email: dev@localhost.com');
        $this->command->info('Password: password');
    }
}
