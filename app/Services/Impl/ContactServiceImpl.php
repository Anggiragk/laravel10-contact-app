<?php
namespace App\Services\Impl;

use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ContactResource;
use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class ContactServiceImpl implements ContactService{
    public function addContact(ContactCreateRequest $request): ContactResource{
        $data = $request->validated();
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return new ContactResource($contact);
    }

    public function getContact(int $idUser, int $idContact) : HttpResponseException|Contact {
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

    public function updateContact(int $contactId, ContactUpdateRequest $request): ContactResource{
        $data = $request->validated();
        $user = Auth::user();
        $contact = $this->getContact($user->id, $contactId);
        $contact->fill($data);
        $contact->save();
        return new ContactResource($contact);
    }

    /**
     * Delete contact from database
     *
     * @param integer $contactId
     * @return boolean
     */
    public function deleteContact(int $contactId): bool{
        $user = Auth::user();
        $contact = $this->getContact($user->id, $contactId);
        return $contact->delete();
    }

    /**
     * Search Contact
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return ContactCollection
     */
    public function searchContact(Request $request): HttpResponseException|ContactCollection{
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

        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->input('name');
            if($name){
                $builder->orWhere(function (Builder $builder) use ($name) {
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
