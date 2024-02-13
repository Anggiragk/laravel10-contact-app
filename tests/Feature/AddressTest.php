<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\UserSeeder;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressTest extends TestCase
{
    public function testCreateaddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->where("email", '=', "test1@gmail.com")->first();
        $data = [
            "street" => "street test",
            "city" => "city test",
            "country" => "country test",
            "postal_code" => "postal code test",
        ];
        $response = $this->post('/api/contacts/' . ($contact->id) . '/addresses', $data, [
            "Authorization" => "test"
        ]);
        $response->assertStatus(201);
        $response->assertJson([
            "data" => $data
        ]);
    }

    public function testCreateaddressFailed()
    {
        $this->seed(UserSeeder::class);
        $data = [
            "street" => "",
            "city" => "",
            "country" => "country test",
            "postal_code" => "postal code test",
        ];
        $response = $this->post('/api/contacts/' . (9999999) . '/addresses', $data, [
            "Authorization" => "test"
        ]);
        $response->assertStatus(400);
        $response->assertJson([
            "errors" => [
                "street" => [
                    "The street field is required.",
                ],
                "city" => [
                    "The city field is required."
                ]

            ]
        ]);
    }

    public function testGetAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->where('street', '=', 'street 1 test')->first();

        $this->get("api/contacts/$address->contact_id/addresses/$address->id", [
            "Authorization" => "test"
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "street" => "street 1 test",
                    "city" => "city 1 test",
                    "country" => "country 1 test",
                    "postal_code" => "11111"
                ]
            ]);
    }

    public function testGetAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->where('street', '=', 'street 2 test')->first();

        $this->get("api/contacts/$address->contact_id/addresses/$address->id", [
            "Authorization" => "test"
        ])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "messages" => [
                        "Contact not found"
                    ]
                ]
            ]);
    }

    public function testUpdateAddressSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->where('street', '=', 'street 1 test')->first();
        $data = [
            "street" => "new street",
            "city" => "new city",
            "country" => "country test",
            "postal_code" => "postal code test",
        ];
        $this->put("api/contacts/$address->contact_id/addresses/$address->id",$data, [
            "Authorization" => "test"
        ])
        ->assertStatus(200);
    }

    public function testUpdateAddressFailed(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->where('street', '=', 'street 1 test')->first();
        $data = [
            "street" => "new street",
            "city" => "new city",
            "country" => "country test",
            "postal_code" => "",
        ];
        $this->put("api/contacts/$address->contact_id/addresses/$address->id",$data, [
            "Authorization" => "test"
        ])
        ->assertStatus(400)
        ->assertJson([
            "errors"=> [
                "postal_code" => [
                    "The postal code field is required."
                ]
            ]
        ]);
    }

    public function testDeleteAddressSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->where('street', '=', 'street 1 test')->first();
        $this->delete("api/contacts/$address->contact_id/addresses/$address->id",[], [
            "Authorization" => "test"
        ])
        ->assertStatus(200)
        ->assertJson([
            "data"=> true
        ]);
    }

    public function testDeleteAddressFailed(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->where('street', '=', 'street 1 test')->first();
        $this->delete("api/contacts/$address->contact_id/addresses/999999999",[], [
            "Authorization" => "test"
        ])
        ->assertStatus(404)
        ->assertJson([
            "errors"=> [
                "messages" => [
                    "Address not found"
                ]
            ]
        ]);
    }

    public function testListAddressSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->where('email', '=', 'test1@gmail.com')->first();
        $response = $this->get("api/contacts/$contact->id/addresses", [
            "Authorization" => "test"
        ])
        ->assertStatus(200)
        ->json();

        self::assertCount(3, $response['data']);

    }

    public function testListAddressContactNotFound(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $this->get("api/contacts/999999999/addresses", [
            "Authorization" => "test"
        ])
        ->assertStatus(404)
        ->assertJson([
            "errors" => [
                "messages" => [
                    "Contact not found"
                ]
            ]
        ]);

    }
}
