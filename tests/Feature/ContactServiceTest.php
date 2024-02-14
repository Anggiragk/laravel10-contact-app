<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactServiceTest extends TestCase
{
    private ContactService $contactService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contactService = $this->app->make(ContactService::class);
    }

    public function testContactService()
    {
        self::assertNotNull($this->contactService);
    }

    public function testContactServiceIsSingleton(){
        $contactService1 = $this->app->make(ContactService::class);
        $contactService2 = $this->app->make(ContactService::class);

        self::assertSame($contactService1, $contactService2);
    }
}
