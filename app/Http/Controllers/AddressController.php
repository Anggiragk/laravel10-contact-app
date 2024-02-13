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
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{

    private function getContact(int $idUser, int $idContact) : null|Contact {
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

    private function getAddress(int $idAddress) : null|Address{
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

    public function create(int $idContact, AddressRequest $request): JsonResponse{
        $user = Auth::user();
        $contact = $this->getContact($user->id, $idContact);

        $data =$request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $idContact, int $idAddress): AddressResource{
        $user = Auth::user();
        $contact = $this->getContact($user->id, $idContact);
        $address = $this->getAddress($idAddress);
        return new AddressResource($address);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request) : AddressResource {
        $user = Auth::user();
        $contact = $this->getContact($user->id, $idContact);
        $address = $this->getAddress($idAddress);

        $data = $request->validated();
        $address->fill($data);
        $address->save();
        return new AddressResource($address);
    }

    public function delete(int $idContact, int $idAddress) : JsonResponse {
        $user = Auth::user();
        $contact = $this->getContact($user->id, $idContact);
        $address = $this->getAddress($idAddress);

        $address->delete();
        return response()->json([
            "data" => true
        ], 200);
    }

    public function list(int $idContact) : JsonResponse {
        $user = Auth::user();
        $contact = $this->getContact($user->id, $idContact);
        $addresses = Address::query()->where('contact_id', '=', $idContact)->get();

        return (AddressResource::collection($addresses))->response()->setStatusCode(200);

    }
}
