<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $authenticate = true;
        $token = $request->header('Authorization');
        $user = User::query()->where('token', '=', $token)->first();

        if ((!$token) || (!$user)) {
            $authenticate = false;
        }else{
            Auth::login($user);
        }

        if ($authenticate) {
            return $next($request);
        } else {
            return response()->json(
                [
                    'errors' => [
                        "messages" => [
                            "unauthorized"
                        ]
                    ]
                ],
                401
            );
        }
    }
}
