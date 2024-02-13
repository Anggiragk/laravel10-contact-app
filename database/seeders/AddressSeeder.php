<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contact1 = Contact::query()->where('email', '=', 'test1@gmail.com')->first();
        $contact2 = Contact::query()->where('email', '=', 'test2@gmail.com')->first();

        Address::create([
            "street" => "street 1 test",
            "city" => "city 1 test",
            "country" => "country 1 test",
            "postal_code" => "11111",
            "contact_id" => $contact1->id
        ]);

        Address::create([
            "street" => "New street 333",
            "city" => "City 333",
            "country" => "country 333",
            "postal_code" => "333",
            "contact_id" => $contact1->id
        ]);

        Address::create([
            "street" => "New street 444",
            "city" => "City 444",
            "country" => "country 444",
            "postal_code" => "444",
            "contact_id" => $contact1->id
        ]);

        Address::create([
            "street" => "street 2 test",
            "city" => "city 2 test",
            "country" => "country 2 test",
            "postal_code" => "22222",
            "contact_id" => $contact2->id
        ]);
    }
}
