<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserRegisterRequest;

interface UserService{
    public function addUser(UserRegisterRequest $request): UserResource;
    public function updateUser(UserUpdateRequest $request): UserResource;
    public function login(UserLoginRequest $request): UserResource;
    public function logout(): bool;
    public function getUser();

}
