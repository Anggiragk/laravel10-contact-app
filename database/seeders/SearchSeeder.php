<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::query()->where('username', '=', 'test')->first();

        // Contact::create(
        //     [
        //         "first_name" => "test1 first",
        //         "last_name" => "test1 last",
        //         "email" => "test1@gmail.com",
        //         "phone" => "08111111",
        //         "user_id" => $user->id
        //     ]
        // );
        for ($i = 1; $i <= 15; $i++) {
            Contact::create(
                [
                    "first_name" => "first $i",
                    "last_name" => "last $i",
                    "email" => "email$i@gmail.com",
                    "phone" => "0811111$i",
                    "user_id" => $user->id
                ]
            );
        }
    }
}
