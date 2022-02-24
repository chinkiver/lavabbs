<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::factory()->count(10)->create();

        // 创建站长
        $admin = User::find(1);
        $admin->name = 'admin';
        $admin->username = '超级管理员';
        $admin->assignRole('Founder');
        $admin->save();

        // 创建管理员
        $manager = User::find(2);
        $manager->name = 'manager';
        $manager->username = '管理员';
        $manager->assignRole('Maintainer');
        $manager->save();
    }
}
