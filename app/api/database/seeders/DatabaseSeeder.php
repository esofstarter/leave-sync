<?php

namespace Database\Seeders;

use App\Applications\User\Model\User;
use App\Constants\UserPermissions;
use App\Constants\UserRoles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Users
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Userot',
            'email' => 'admin@example.com',
            'password' => Hash::make('password')
        ]);

        $manager = User::create([
            'first_name' => 'Manager',
            'last_name' => 'Userot',
            'email' => 'manager@example.com',
            'password' => Hash::make('password')
        ]);

        $developer = User::create([
            'first_name' => 'Developer',
            'last_name' => 'Userot',
            'email' => 'developer@example.com',
            'password' => Hash::make('password')
        ]);

        $collaborator = User::create([
            'first_name' => 'Collaborator',
            'last_name' => 'Macedonia',
            'email' => 'collaborator@example.com',
            'password' => Hash::make('password')
        ]);

        $collaboratorBG = User::create([
            'first_name' => 'Collaborator',
            'last_name' => 'Bulgaria',
            'email' => 'collaboratorBG@example.com',
            'password' => Hash::make('password')
        ]);

        // Create Permissions
        $permissions = [
            UserPermissions::READ_USERS,
            UserPermissions::WRITE_USERS,
            UserPermissions::DELETE_USERS,
            UserPermissions::WRITE_PROFILE,
            UserPermissions::READ_REQUESTS,
            UserPermissions::APPROVE_REQUESTS,
            UserPermissions::WRITE_REQUESTS,
            UserPermissions::DELETE_REQUESTS,
            UserPermissions::DASHBOARD_VIEW,
            UserPermissions::ALL_REQUESTS,
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        $roleAdmin = Role::create(['name' => UserRoles::ADMIN])
            ->givePermissionTo(Permission::all());

        $roleManager = Role::create(['name' => UserRoles::MANAGER])
            ->givePermissionTo([
                UserPermissions::DASHBOARD_VIEW,
                UserPermissions::READ_USERS,
                UserPermissions::WRITE_USERS,
                UserPermissions::WRITE_PROFILE,
                UserPermissions::READ_REQUESTS,
                UserPermissions::WRITE_REQUESTS,
                UserPermissions::DELETE_REQUESTS,
                UserPermissions::APPROVE_REQUESTS,
                UserPermissions::ALL_REQUESTS
            ]);

        $roleDeveloper = Role::create(['name' => UserRoles::DEVELOPER])
            ->givePermissionTo([
                UserPermissions::DASHBOARD_VIEW,
                UserPermissions::READ_REQUESTS,
                UserPermissions::WRITE_PROFILE,
                UserPermissions::WRITE_REQUESTS,
                UserPermissions::DELETE_REQUESTS,
            ]);

        $roleCollaborator = Role::create(['name' => UserRoles::COLLABORATOR])
            ->givePermissionTo([
                UserPermissions::DASHBOARD_VIEW,
                UserPermissions::READ_USERS,
                UserPermissions::READ_REQUESTS,
                UserPermissions::WRITE_PROFILE,
                UserPermissions::ALL_REQUESTS
            ]);

        // Assign Roles to Users
        $admin->assignRole(UserRoles::ADMIN);
        $manager->assignRole(UserRoles::MANAGER);
        $developer->assignRole(UserRoles::DEVELOPER);
        $collaborator->assignRole(UserRoles::COLLABORATOR);
        $collaboratorBG->assignRole(UserRoles::COLLABORATOR);

        // Seed Leave Types
        $leaveTypes = [
            ['name' => 'Sick (unpaid)', 'slug' => Str::slug('Sick unpaid'), 'is_paid' => false, 'color' => '#FF6B6B'],
            ['name' => 'Sick (paid)', 'slug' => Str::slug('Sick paid'), 'is_paid' => true, 'color' => '#FFA726'],
            ['name' => 'Vacation Day (paid)', 'slug' => Str::slug('Vacation Day paid'), 'is_paid' => true, 'color' => '#4CAF50'],
            ['name' => 'Vacation Day (unpaid)', 'slug' => Str::slug('Vacation Day unpaid'), 'is_paid' => false, 'color' => '#90A4AE'],
            ['name' => 'National Holiday (paid)', 'slug' => Str::slug('National Holiday paid'), 'is_paid' => true, 'color' => '#6326F2'],
            ['name' => 'Work From Home', 'slug' => Str::slug('Work From Home'), 'is_paid' => true, 'color' => '#ECCBCB'],
        ];

        DB::table('leave_types')->insert($leaveTypes);

        $countries = [
            ['name' => 'Macedonia', 'country_code' => 'MK'],
            ['name' => 'Bulgaria', 'country_code' => 'BG'],
        ];

        DB::table('countries')->insert($countries);
    }
}
