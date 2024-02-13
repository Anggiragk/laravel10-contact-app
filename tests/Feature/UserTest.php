<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;

use function PHPUnit\Framework\assertJson;
use function PHPUnit\Framework\assertNotNull;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{

    public function testUserRegisterSuccess()
    {
        $data = [
            "username" => "alpha",
            "password" => "secret",
            "name" => "Alpha"
        ];

        $response = $this->post("/api/users", $data);

        $response->assertStatus(201);
        $response->assertJson([
            "data" => [
                "username" => "alpha",
                "name" => "Alpha"
            ]
        ]);
    }

    public function testUserRegisterFailed()
    {
        $data = [
            "username" => "alpha",
            "password" => "",
            "name" => "Alpha"
        ];

        $response = $this->post("/api/users", $data);

        $response->assertStatus(400);
        $response->assertJson([
            "errors" => [
                "password" => [
                    "The password field is required."
                ],
            ]
        ]);
    }

    public function testUserRegisterUsernameAlreadyRegistered()
    {
        $data = [
            "username" => "alpha",
            "password" => "secret",
            "name" => "Alpha"
        ];

        $response = $this->post("/api/users", $data);
        $response->assertStatus(201);

        $response = $this->post("/api/users", $data);
        $response->assertStatus(400);
        $response->assertJson([
            "errors" => [
                "username" => [
                    "The username has already been taken."
                ],
            ]
        ]);
    }

    public function testUserLoginSuccess()
    {
        $this->seed(UserSeeder::class);

        $data = [
            "username" => "test",
            "password" => "test",
        ];

        $response = $this->post('/api/users/login', $data);
        $response->assertStatus(200);
        $response->assertJson([
            "data" => [
                "username" => "test",
                "name" => "test",
            ]
        ]);

        $user = User::query()->where('username', '=', 'test')->first();
        self::assertNotNull($user->token);
    }

    public function testUserLoginPasswordWrong()
    {
        $this->seed(UserSeeder::class);

        $data = [
            "username" => "test",
            "password" => "hello",
        ];

        $response = $this->post('/api/users/login', $data);
        $response->assertStatus(401);
        $response->assertJson([
            "errors" => [
                "messages" => [
                    "username or password wrong"
                ]
            ]
        ]);
    }

    public function testGetUserSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/users/current', [
            "Authorization" => "test"
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test",
                    "token" => "test",
                ]
            ]);
    }

    public function testGetUserFailed()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "messages" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testGetUserInvalidAuthorization()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/users/current', [
            "Authorization" => "wrong"
        ])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "messages" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testUserUpdateNameSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::query()->where('username', '=', 'test')->first();
        $data = [
            "name" => "alpha",
        ];
        $headers = [
            "Authorization" => "test"
        ];
        $this->patch('/api/users/current', $data, $headers)
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "alpha"
                ]
            ]);
        $newUser = User::query()->where('username', '=', 'test')->first();
        self::assertSame($oldUser->password, $newUser->password);
        self::assertNotSame($oldUser->name, $newUser->name);
    }

    public function testUserUpdatePasswordSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUserPassword = User::query()->where('username', '=', 'test')->first("password");
        $data = [
            "password" => "test"
        ];
        $headers = [
            "Authorization" => "test"
        ];
        $this->patch('/api/users/current', $data, $headers)
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "test"
                ]
            ]);

        $newUserPassword = User::query()->where('username', '=', 'test')->first("password");

        self::assertNotSame($oldUserPassword->password, $newUserPassword->password);
    }

    public function testUserUpdateFailed()
    {
        $this->seed(UserSeeder::class);

        $data = [
            "name" => Str::random(101),
            "password" => Str::random(101),
        ];

        $headers = [
            "Authorization" => "test"
        ];
        $this->patch('/api/users/current', $data, $headers)
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name must not be greater than 100 characters."
                    ],
                    "password" => [
                        "The password must not be greater than 100 characters."
                    ]
                ]
            ]);
    }

    public function testUserUpdateFailedInvalidAuth()
    {
        $this->seed(UserSeeder::class);
        $headers = [
            "Authorization" => "wrong"
        ];
        $this->patch('/api/users/current', [], $headers)
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "messages" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testUserLogoutSuccess(){
        $this->seed(UserSeeder::class);

        $this->delete("/api/users/logout",[], ["Authorization" => "test"])
        ->assertStatus(200)
        ->assertJson([
            "data" => true
        ]);

        $user = User::query()->where("username", "=", "test")->first();
        self::assertNull($user->token);
    }

    public function testUserLogoutFailed(){
        $this->seed(UserSeeder::class);

        $this->delete("/api/users/logout")
        ->assertStatus(401)
        ->assertJson([
            "errors" => [
                "messages" => [
                    "unauthorized"
                ]
            ]
        ]);
    }
}
