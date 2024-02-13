<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateContact()
    {
        $this->seed(UserSeeder::class);

        $data = [
            "first_name" => "foo",
            "last_name" => "bar",
            "email" => "foo@gmail.com",
            "phone" => "0123456789",
        ];

        $this->post("/api/contacts", $data, [
            "Authorization" => "test"
        ])
        ->assertStatus(201)
        ->assertJson([
            "data" => $data
        ]);
    }

    public function testCreateContactFailed()
    {
        $this->seed(UserSeeder::class);

        $data = [
            "first_name" => "",
            "last_name" => "bar",
            "email" => "foo",
            "phone" => "0123456789",
        ];

        $this->post("/api/contacts", $data, [
            "Authorization" => "test"
        ])
        ->assertStatus(400)
        ->assertJson([
            "errors" => [
                "first_name" => [
                    "The first name field is required."
                ],
                "email" => [
                    "The email must be a valid email address."
                ]
            ]
        ]);
    }

    public function testGetContactSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact1 = Contact::query()->where("email", "=", "test1@gmail.com")->first();
        $this->get("/api/contacts/".$contact1->id ,[
            "Authorization" => "test"
        ])
        ->assertStatus(200);
    }

    public function testGetContactFailed(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact1 = Contact::query()->where("email", "=", "test1@gmail.com")->first();
        $this->get("/api/contacts/".$contact1->id ,[
            "Authorization" => ""
        ])
        ->assertStatus(401);
    }

    public function testGetContactFromAnotherUser(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact1 = Contact::query()->where("email", "=", "test2@gmail.com")->first();
        $this->get("/api/contacts/".$contact1->id ,[
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

    public function testUpdateSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->where("email", "=", "test1@gmail.com")->first();
        $data = [
            "first_name" => "foo",
            "last_name" => "bar",
            "email" => "foo@gmail.com",
            "phone" => "0123456789",
        ];

        $this->put("/api/contacts/".($contact->id), $data, [
            "Authorization" => "test"
        ])
        ->assertStatus(200)
        ->assertJson([
            "data" => $data
        ]);
    }

    public function testUpdateFailedIdContactNotFound(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->where("email", "=", "test1@gmail.com")->first();
        $data = [
            "first_name" => "foo",
            "last_name" => "bar",
            "email" => "foo@gmail.com",
            "phone" => "0123456789",
        ];

        $this->put("/api/contacts/".(999999), $data, [
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

    public function testDeleteSuccess() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->where("email", "=", "test1@gmail.com")->first();
        $this->delete('/api/contacts/'.($contact->id),[], [
            "Authorization" => "test"
        ])
        ->assertStatus(200)
        ->assertJson([
            "data" => true
        ]);
    }

    public function testDeleteFailed() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $this->delete('/api/contacts/'.(99999999),[], [
            "Authorization" => "test"
        ])
        ->assertStatus(404)
        ->assertJson([
            "errors" => [
                "messages" => ["Contact not found"]
            ]
        ]);
    }

    public function testSearchAll(){
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?size=15',[
            "Authorization" => "test"
        ])->assertStatus(200)
        ->json();

        self::assertCount(15, $response['data']);
    }

    public function testSearchName(){
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=first',[
            "Authorization" => "test"
        ])->assertStatus(200)
        ->json();

        self::assertCount(5, $response['data']);
    }

    public function testSearcEmail(){
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=email1@gmail.com',[
            "Authorization" => "test"
        ])->assertStatus(200)
        ->json();

        self::assertCount(1, $response['data']);
    }

    public function testSearcPhone(){
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=08111112',[
            "Authorization" => "test"
        ])->assertStatus(200)
        ->json();

        self::assertCount(1, $response['data']);
    }
}
