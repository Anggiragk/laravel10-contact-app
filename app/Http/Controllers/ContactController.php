<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Http\Resources\UserResource;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    private ContactService $contactService;

    public function __construct(ContactService $contactService) {
        $this->contactService = $contactService;
    }

    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $this->contactService->addContact($request);
        return $data->response()->setStatusCode(201);
    }

    public function get(int $contactId): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->contactService->getContact($user->id, $contactId);
        return (new ContactResource($contact))->response()->setStatusCode(200);
    }

    public function update(int $contactId, ContactUpdateRequest $request): JsonResponse
    {
        $data = $this->contactService->updateContact($contactId, $request);
        return $data->response()->setStatusCode(200);
    }

    public function delete(int $contactId): JsonResponse{
        $data = $this->contactService->deleteContact($contactId);
        return response()->json([
            "data" => $data
        ],200);
    }

    public function search(Request $request): JsonResponse{
        $data = $this->contactService->searchContact($request);
        return $data->response()->setStatusCode(200);
    }
}
