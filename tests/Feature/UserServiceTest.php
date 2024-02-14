<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\UserService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make(UserService::class);
    }

    public function testUserService() {
        self::assertNotNull($this->userService);
    }

    public function testUserServiceIsSingleton()
    {
        $user1 = $this->app->make(UserService::class);
        $user2 = $this->app->make(UserService::class);
        self::assertSame($user1, $user2);
    }
}
