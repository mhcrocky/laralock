<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Models\Auth\User;
use App\Models\Auth\UserBiodata;
use App\Models\Auth\UserStatus;


class UserFakerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $newUser = [];
        $newUserBiodata = [];
        $newUserStatus = [];
        for ($i = 0; $i < env('SEED_MORE_USER', 50); $i++) {
            $moreUser = [
                'code' => User_createNewCode(),
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => '$2y$10$0F3q8CDeDiEhqGlfxWmGPeqLLA7f5AsIgV.MZ6NoouGKaUzV0ZaXq', // @UserTest10
                'active' => User_setActiveStatus('active'),
                'status' => User_setStatus('user'),
                'profile_img' => default_user_image()
            ];
            $newUser[] = [
                'email' => $moreUser['email'],
                'email_verified_at' => Carbon_DBtimeNow(),
                'password' => $moreUser['password'],
                'code' => $moreUser['code'],
                'active' => $moreUser['active']
            ];
            $newUserBiodata[] = [
                'code' => $moreUser['code'],
                'name' => $moreUser['name'],
                'profile_img' => $moreUser['profile_img']
            ];
            $newUserStatus[] = [
                'code' => $moreUser['code'],
                'status' => $moreUser['status']
            ];
        }
        foreach (array_chunk($newUser, 5000) as $setUser) {
            User::insert($setUser);
        }
        foreach (array_chunk($newUserBiodata, 5000) as $setUserBiodata) {
            UserBiodata::insert($setUserBiodata);
        }
        foreach (array_chunk($newUserStatus, 5000) as $setUserStatus) {
            UserStatus::insert($setUserStatus);
        }
    }
}
