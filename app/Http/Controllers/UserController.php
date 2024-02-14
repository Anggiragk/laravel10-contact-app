<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function register(UserRegisterRequest $request): JsonResponse  {
        $data = $this->userService->addUser($request);
        return $data->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request) : JsonResponse {
        $data = $this->userService->login($request);        return $data->response()->setStatusCode(200);
    }

    public function get(): JsonResponse{
        $data = $this->userService->getUser();
        return $data->response()->setStatusCode(200);
    }

    public function update(UserUpdateRequest $request): JsonResponse{
        $data = $this->userService->updateUser($request);
        return $data->response()->setStatusCode(200);
    }

    public function logout(): JsonResponse{
        $data = $this->userService->logout();
        return response()->json([
            "data" => $data
        ], 200);
    }
}
