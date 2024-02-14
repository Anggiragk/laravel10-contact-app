<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{

    private AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    private function getContact(int $idUser, int $idContact): null|Contact
    {
        $contact = Contact::query()
            ->where("id", "=", $idContact)
            ->where("user_id", "=", $idUser)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json(
                [
                    "errors" => [
                        "messages" => [
                            "Contact not found"
                        ]
                    ]
                ],
                404
            ));
        }
        return $contact;
    }

    private function getAddress(int $idAddress): null|Address
    {
        $address = Address::query()->where('id', '=', $idAddress)->first();
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
        return $address;
    }

    public function create(int $contactId, AddressRequest $request): JsonResponse
    {
        $data = $this->addressService->createAddress($contactId, $request);
        return $data->response()->setStatusCode(201);
    }

    public function get(int $contactId, int $addressId): JsonResponse
    {
        $data = $this->addressService->getAddress($contactId, $addressId);
        return $data->response()->setStatusCode(200);
    }

    public function update(int $contactId, int $addressId, AddressUpdateRequest $request): JsonResponse
    {
        $data = $this->addressService->updateAddress($contactId, $addressId, $request);
        return $data->response()->setStatusCode(200);
    }

    public function delete(int $contactId, int $addressId): JsonResponse
    {
        $data = $this->addressService->deleteAddress($contactId, $addressId);
        return response()->json([
            "data" => $data
        ], 200);
    }

    public function list(int $contactId): JsonResponse
    {
        $addresses = $this->addressService->getListAddress($contactId);
        return (AddressResource::collection($addresses))->response()->setStatusCode(200);
    }
}
