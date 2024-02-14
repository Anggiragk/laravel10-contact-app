<?php

namespace Tests\Feature;

use App\Services\AddressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressServiceTest extends TestCase
{
    private AddressService $addressService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->addressService = $this->app->make(AddressService::class);
    }
    public function testAddressService()
    {
        self::assertNotNull($this->addressService);
    }

    public function testAddressServiceIsSingleton(){
        $addressService1 = $this->app->make(AddressService::class);
        $addressService2 = $this->app->make(AddressService::class);

        self::assertSame($addressService1, $addressService2);
    }
}
