<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class asignRole extends Command
{
    protected $signature = 'asignar-rol {user_id : El ID del usuario} {role : El nombre del rol}';

    protected $description = 'Asigna un rol a un usuario';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $userId = $this->argument('user_id');
        $roleName = $this->argument('role');

        $user = User::findOrFail($userId);
        $role = Role::where('name', $roleName)->firstOrFail();

        $user->assignRole($role);

        $this->info("Se ha asignado el rol '$roleName' al usuario ID '$userId'.");
    }
}
