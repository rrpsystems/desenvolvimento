<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{

    public function run()
    {
        //
        User::updateOrCreate([
                'email'     => 'master@tarifador.com',
            ],
            [
                'name'      => 'Master',
                'email'     => 'master@tarifador.com',
                'password'  => '123456789'
        ]);


        User::updateOrCreate([
            'email'     => 'admin@tarifador.com',
        ],
        [
            'name'      => 'Administrador',
            'email'     => 'admin@tarifador.com',
            'password'  => '123456789'
        ]);

        User::updateOrCreate([
            'email'     => 'user@tarifador.com',
        ],
        [
            'name'      => 'Usuario',
            'email'     => 'user@tarifador.com',
            'password'  => '123456789'
        ]);
    }
}
