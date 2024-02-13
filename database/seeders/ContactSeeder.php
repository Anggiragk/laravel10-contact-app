<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::query()->where("username", "=", "test")->first();
        $user2 = User::query()->where("username", "=", "test2")->first();
        Contact::create(
            [
                "first_name" => "test1 first",
                "last_name" => "test1 last",
                "email" => "test1@gmail.com",
                "phone" => "08111111",
                "user_id" => $user1->id

            ]
        );

        Contact::create(
            [
                "first_name" => "test2 first",
                "last_name" => "test2 last",
                "email" => "test2@gmail.com",
                "phone" => "08222222",
                "user_id" => $user2->id
            ]
        );
    }
}
