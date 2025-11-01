<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create personal team for super admin
        if (!$superAdmin->currentTeam) {
            $team = Team::forceCreate([
                'user_id' => $superAdmin->id,
                'name' => $superAdmin->name . "'s Team",
                'personal_team' => true,
            ]);

            $superAdmin->current_team_id = $team->id;
            $superAdmin->save();

            $superAdmin->teams()->attach($team, ['role' => 'owner']);
        }

        // Assign Super Admin role
        $superAdmin->assignRole('Super Admin');

        $this->command->info("Super Admin created: admin@test.com / password");

        // Create Staff User
        $staff = User::firstOrCreate(
            ['email' => 'staff@test.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create personal team for staff
        if (!$staff->currentTeam) {
            $team = Team::forceCreate([
                'user_id' => $staff->id,
                'name' => $staff->name . "'s Team",
                'personal_team' => true,
            ]);

            $staff->current_team_id = $team->id;
            $staff->save();

            $staff->teams()->attach($team, ['role' => 'owner']);
        }

        // Assign Staff role
        $staff->assignRole('Staff');

        $this->command->info("Staff User created: staff@test.com / password");

        // Create Customer User
        $customer = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'Customer User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create personal team for customer
        if (!$customer->currentTeam) {
            $team = Team::forceCreate([
                'user_id' => $customer->id,
                'name' => $customer->name . "'s Team",
                'personal_team' => true,
            ]);

            $customer->current_team_id = $team->id;
            $customer->save();

            $customer->teams()->attach($team, ['role' => 'owner']);
        }

        // Assign Customer role
        $customer->assignRole('Customer');

        $this->command->info("Customer User created: customer@test.com / password");

        $this->command->info('Test users seeded successfully!');
    }
}
