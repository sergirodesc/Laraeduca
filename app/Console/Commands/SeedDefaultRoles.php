<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SeedDefaultRoles extends Command
{
    protected $signature = 'seed:default-roles';
    protected $description = 'Seed default roles using Spatie';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $roles = ['admin', 'teacher', 'student']; 

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
            $this->info("Role '$role' created successfully!");
        }

        $this->info('Default roles seeded successfully!');
    }
}
