<?php

use Illuminate\Database\Seeder;
use App\Models\Auth\User;
use App\Models\Auth\UserBiodata;
use App\Models\Auth\UserStatus;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            ['status' => User_setStatus('admin'), 'name' => 'My Name is Bachtiar', 'email' => 'bachtiar@mail.com', 'email_verified_at' => '2019-12-07 07:27:47', 'password' => '$2y$10$XsG.kSbJA1g15hcgxp/W6Offn8yLS/igQBBVMzTBBuNvFYt5gb14m', 'code' => '5emMFhIv84in6ccx6gW3xfJ6VqT3vtjxpwvakO6LknK99d0fbr615nDKhJEltks2', 'created_at' => '2019-12-07 07:27:02', 'updated_at' => '2019-12-07 07:27:47', 'active' => User_setActiveStatus('active'), 'profile_img' => '/files/image/profile/users/USR-Q3yszDnYjIkXq7G.jpg'],
            ['status' => User_setStatus('user'), 'name' => 'Sister of Bachtiar', 'email' => 'cashier@mail.com', 'email_verified_at' => '2019-12-07 07:34:40', 'password' => '$2y$10$7hZwzXwDNjgQWdder8b3pe7d3s5s.FAAvaKzYydcY7REoD8/aRAcW', 'code' => 'qdBfFeo7grHXlMIFNCcySEbUCJQyahTw4pysFHXdg5HNAvyL9B94TUo9OkuM7GKp', 'created_at' => '2019-12-07 07:33:29', 'updated_at' => '2019-12-07 07:34:40', 'active' => User_setActiveStatus('active'), 'profile_img' => '/files/image/profile/users/USR-Q3yszDnYjIkXq7G.jpg']
        ];

        for ($i = 0; $i < count($user); $i++) {
            User::create([
                'email' => $user[$i]['email'],
                'email_verified_at' => $user[$i]['email_verified_at'],
                'password' => $user[$i]['password'],
                'code' => $user[$i]['code'],
                'active' => $user[$i]['active']
            ]);
            UserBiodata::create([
                'code' => $user[$i]['code'],
                'name' => $user[$i]['name'],
                'profile_img' => $user[$i]['profile_img']
            ]);
            UserStatus::create([
                'code' => $user[$i]['code'],
                'status' => $user[$i]['status']
            ]);
        }
    }
}
