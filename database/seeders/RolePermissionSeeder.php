<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'TicketController@index',
            'TicketController@show',
            'TicketController@reply',
            'TicketController@changeStatus',
            'TicketController@destroy'
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $user = User::query()->create([
            'first_name' => 'Ehsan',
            'last_name' => 'Zanjani',
            'email' => 'ehsan@gmail.com',
            'password' => 'Aa12345@'
        ]);
        $user->assignRole($superAdmin);
    }
}
