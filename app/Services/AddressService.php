<?php
namespace App\Services;

use App\Models\Address;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Http\Requests\AddressUpdateRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;

interface AddressService{
    public function createAddress(int $contactId, AddressRequest $request): AddressResource;
    public function getAddress(int $contactId, int $addressId): HttpResponseException|AddressResource;
    public function updateAddress(int $contactId, int $addressId, AddressUpdateRequest $request): AddressResource;
    public function deleteAddress(int $contactId, int $addressId): HttpResponseException|bool;
    public function getListAddress(int $contactId) : Collection;
}
