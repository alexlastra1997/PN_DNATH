<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // (Opcional) Reinicia caches de permisos/roles
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crea permisos base (ajústalos a tu gusto)
        $permisos = [
            'ver panel admin',
            'ver panel direccion',
            'ver panel traslados',
            'ver panel dein',
        ];
        foreach ($permisos as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // Crea roles
        $roles = [
            'super admin',
            'admin',
            'direccion',
            'analista traslados',
            'analista dein',
            'jefe traslados',
            'jefe dein',
        ];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }

        // Asigna permisos por rol (ejemplo)
        Role::findByName('super admin')->givePermissionTo(Permission::all());
        Role::findByName('admin')->givePermissionTo(['ver panel admin']);
        Role::findByName('direccion')->givePermissionTo(['ver panel direccion']);
        Role::findByName('analista traslados')->givePermissionTo(['ver panel traslados']);
        Role::findByName('jefe traslados')->givePermissionTo(['ver panel traslados']);
        Role::findByName('analista dein')->givePermissionTo(['ver panel dein']);
        Role::findByName('jefe dein')->givePermissionTo(['ver panel dein']);

        // (Opcional) Crea un usuario admin de prueba
        if (!User::where('email', 'admin@example.com')->exists()) {
            $admin = User::create([
                'name' => 'Administrador',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
            ]);
            $admin->assignRole('super admin');
        }
    }
}
