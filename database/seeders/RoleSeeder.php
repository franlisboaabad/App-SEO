<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Define los permisos del sistema agrupados por módulo
     */
    private function getPermissions(): array
    {
        return [
            'dashboard' => [
                'admin.home' => 'Ver Dashboard',
            ],
            'roles' => [
                'admin.roles.index' => 'Lista de roles',
                'admin.roles.create' => 'Registrar rol',
                'admin.roles.edit' => 'Editar rol',
                'admin.roles.destroy' => 'Eliminar rol',
            ],
            'usuarios' => [
                'admin.usuarios.index' => 'Lista de usuarios',
                'admin.usuarios.edit' => 'Editar usuario',
                'admin.usuarios.update' => 'Actualizar usuario y asignar roles',
            ],
            'sites' => [
                'admin.sites.index' => 'Lista de sitios',
                'admin.sites.create' => 'Crear sitio',
                'admin.sites.edit' => 'Editar sitio',
                'admin.sites.show' => 'Ver detalles del sitio',
                'admin.sites.dashboard' => 'Ver dashboard SEO',
                'admin.sites.destroy' => 'Eliminar sitio',
            ],
            'audits' => [
                'admin.audits.index' => 'Ver auditorías',
                'admin.audits.show' => 'Ver detalles de auditoría',
                'admin.audits.run' => 'Ejecutar auditoría',
            ],
            'keywords' => [
                'admin.keywords.index' => 'Lista de keywords',
                'admin.keywords.create' => 'Crear keyword',
                'admin.keywords.edit' => 'Editar keyword',
                'admin.keywords.show' => 'Ver detalles de keyword',
                'admin.keywords.dashboard' => 'Ver dashboard de keyword',
                'admin.keywords.destroy' => 'Eliminar keyword',
            ],
            'tasks' => [
                'admin.tasks.index' => 'Lista de tareas',
                'admin.tasks.create' => 'Crear tarea',
                'admin.tasks.edit' => 'Editar tarea',
                'admin.tasks.show' => 'Ver detalles de tarea',
                'admin.tasks.destroy' => 'Eliminar tarea',
            ],
            'competitors' => [
                'admin.competitors.index' => 'Lista de competidores',
                'admin.competitors.create' => 'Crear competidor',
                'admin.competitors.edit' => 'Editar competidor',
                'admin.competitors.show' => 'Ver detalles de competidor',
                'admin.competitors.dashboard' => 'Ver dashboard de competencia',
                'admin.competitors.destroy' => 'Eliminar competidor',
            ],
        ];
    }

    /**
     * Define los roles y sus permisos asociados
     */
    private function getRolePermissions(): array
    {
        return [
            'Admin' => ['*'], // El admin tiene acceso a todo
            'Colaborador' => ['admin.home'], // El colaborador solo tiene acceso al dashboard
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Crear roles
        $roles = [];
        foreach ($this->getRolePermissions() as $roleName => $permissions) {
            $roles[$roleName] = Role::firstOrCreate(['name' => $roleName]);
        }

        // Crear y sincronizar permisos
        foreach ($this->getPermissions() as $module => $modulePermissions) {
            foreach ($modulePermissions as $permissionName => $description) {
                $permission = Permission::firstOrCreate(
                    ['name' => $permissionName],
                    ['description' => $description]
                );

                // Asignar permisos a roles
                foreach ($this->getRolePermissions() as $roleName => $rolePermissions) {
                    if (in_array('*', $rolePermissions) || in_array($permissionName, $rolePermissions)) {
                        $roles[$roleName]->givePermissionTo($permission);
                    }
                }
            }
        }
    }
}
