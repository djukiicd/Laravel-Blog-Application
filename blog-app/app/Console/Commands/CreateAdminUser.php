<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'role' => 'admin',
                'password' => Hash::make($password)
            ]);
            $this->info("User {$email} has been updated to admin role.");
        } else {
            User::create([
                'name' => 'Admin User',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
            ]);
            $this->info("Admin user {$email} has been created.");
        }
    }
}