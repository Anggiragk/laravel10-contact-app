<?php
namespace App\Services;

use App\Models\Contact;
use App\Http\Resources\ContactResource;
use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

/**
 * Contact service
 */
interface ContactService{
    public function addContact(ContactCreateRequest $request): ContactResource;
    public function getContact(int $userId, int $contactId): HttpResponseException|Contact;
    public function updateContact(int $contactId, ContactUpdateRequest $request): ContactResource;
    public function deleteContact(int $contactId): bool;
    public function searchContact(Request $request): HttpResponseException|ContactCollection;

}
