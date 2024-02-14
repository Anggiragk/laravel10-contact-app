<?php
namespace App\Services\Impl;

use App\Models\User;
use Illuminate\Support\Str;
use App\Services\UserService;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserServiceImpl implements UserService{

    public function addUser(UserRegisterRequest $request): UserResource{
        $data = $request->validated();

        $user = new User($data);
        $user['password']= Hash::make($data['password']);
        $user->save();

        return new UserResource($user);
    }

    public function login(UserLoginRequest $request): UserResource{
        $data = $request->validated();

        $login = Auth::attempt($data);
        if (!$login) {
            throw new HttpResponseException(response([
                'errors' => [
                    'messages' => [
                        "username or password wrong"
                    ]
                ]
            ], 401));
        }

        $user = User::query()->where('username', '=', $data['username'])->first();
        $user->token = Str::uuid()->tostring();
        $user->save();
        return new UserResource($user);
    }

    public function getUser(){
        $user = Auth::user();
        return new UserResource($user);
    }

    public function updateUser(UserUpdateRequest $request): UserResource{
        $data = $request->validated();
        $user = Auth::user();

        if(isset($data["name"])){
            $user->name = $data["name"];
        }

        if(isset($data["password"])){
            $user->password = Hash::make($data["password"]);
        }
        $user->save();

        return new UserResource($user);
    }

    public function logout(): bool
    {
        $user = Auth::user();
        $user->token = null;
        Auth::logout();
        return $user->save();
    }





}
