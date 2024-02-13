<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Http\Resources\UserResource;
use App\Models\Contact;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
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

    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = Auth::user();
        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(int $contactId): ContactResource
    {
        $user = Auth::user();
        $contact = $this->getContact($user->id, $contactId);
        return new ContactResource($contact);
    }

    public function update(int $contactId, ContactUpdateRequest $request): ContactResource
    {
        $user = Auth::user();
        $contact = $this->getContact($user->id, $contactId);
        $data = $request->validated();
        $contact->fill($data);
        $contact->save();
        return new ContactResource($contact);
    }

    public function delete(int $contactId): JsonResponse{
        $user = Auth::user();
        $contact = $this->getContact($user->id, $contactId);
        $contact->delete();
        return response()->json([
            "data" => true
        ],200);
    }

    public function search(Request $request): ContactCollection{
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input('size', 5);

        $contacts = Contact::query()->where('user_id', '=', $user->id);

        if (!$contacts) {
            throw new HttpResponseException(response()->json(
                [
                    "errors" => [
                        "messages" => [
                            "contact not found"
                        ]
                    ]
                ],
                404
            ));
        }

        $contacts = $contacts->where(function (EloquentBuilder $builder) use ($request) {
            $name = $request->input('name');
            if($name){
                $builder->orWhere(function (EloquentBuilder $builder) use ($name) {
                    $builder->orWhere('first_name', 'like', "%$name%");
                    $builder->orWhere('last_name', 'like', "%$name%");
                });
            }

            $email = $request->input('email');
            if($email){
                $builder->orWhere('email', 'like', "%$email%");
            };

            $phone = $request->input('phone');
            if($phone){
                $builder->orWhere('phone', 'like', "%$phone%");
            }
        });

        $contacts = $contacts->paginate(perPage:$size, page:$page);

        return new ContactCollection($contacts);
    }
}
