<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     * Use example:
     * php artisan make:user "foo" "foo@email.com" "foopassword" "employee"
     *
     * @var string
     */
    protected $signature = 'user:make
                            {name=test : The name of the user}
                            {email=test@email.com : The email of the user}
                            {password=123456789 : The password of the user}
                            {role=employee : The role of the user (default: employee)|(employee, manager)}
                            {is_active=1 : Is the user active? (default: 1)}
                            {address=null : The address of the user (default: null)}
                            {phone=null : The phone number of the user}
                            {birthdate=null : The birthdate of the user (default: null)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user(employee) for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /**
         * User creation
         */
        $name = $this->argument('name');
        $email = $this->argument('email');
        $passwordArg = $this->argument('password');
        $role = $this->argument('role');

        //Ask password if not provided in argument
        $password = $passwordArg ?: $this->secret('Enter the password for the user');

        //Validator
        $validator = Validator::make(
            compact('name', 'email', 'password'),
            [
                'name' => 'required|string|max:100',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required|string|min:6',
                'role' => 'in:employee,manager',
            ]
        );

        if ($validator->fails()){
            foreach ($validator->errors()->all() as $err){
                $this->error($err);
            }
            return 1;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'is_active' => true,
        ]);

        //Success message
        $this->info("User {$user->name} was sucessfully created with ID {$user->id}");
        return self::SUCCESS;
    }
}
