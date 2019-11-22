<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create([
            'name'      => 'Super Usuario Root',
            'email'     => 'root@root.com',
            'password'  => '123456789'
        ]);


        User::create([
            'name'      => 'Administrador',
            'email'     => 'admin@admin.com',
            'password'  => '123456789'
        ]);

        User::create([
            'name'      => 'Usuario',
            'email'     => 'user@user.com',
            'password'  => '123456789'
        ]);
    }
}
