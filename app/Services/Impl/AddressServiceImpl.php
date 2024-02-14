<?php

namespace App\Services\Impl;

use App\Models\Address;
use App\Services\AddressService;
use App\Services\ContactService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressServiceImpl implements AddressService
{
    private ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function createAddress(int $contactId, AddressRequest $request): AddressResource
    {
        $user = Auth::user();
        $contact = $this->contactService->getContact($user->id, $contactId);

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return new AddressResource($address);
    }

    public function getAddress(int $contactId, int $addressId): HttpResponseException|AddressResource
    {
        //check contact
        $user = Auth::user();
        $contact = $this->contactService->getContact($user->id, $contactId);

        $address = Address::query()->where('id', '=', $addressId)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json(
                [
                    "errors" => [
                        "messages" => [
                            "Address not found"
                        ]
                    ]
                ],
                404
            ));
        }
        return new AddressResource($address);
    }

    public function updateAddress(int $contactId, int $addressId, AddressUpdateRequest $request): AddressResource{
        $address = $this->getAddress($contactId, $addressId);

        $data = $request->validated();
        $address->fill($data);
        $address->save();
        return new AddressResource($address);
    }

    public function deleteAddress(int $contactId, int $addressId): HttpResponseException|bool{
        $address = $this->getAddress($contactId, $addressId);
        return $address->delete();
    }

    public function getListAddress(int $contactId) : Collection{
        $user = Auth::user();
        $contact = $this->contactService->getContact($user->id, $contactId);
        $addresses = Address::query()->where('contact_id', '=', $contactId)->get();
        return $addresses;
    }


}
