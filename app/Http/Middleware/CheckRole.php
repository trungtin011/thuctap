<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $userRoleName = $user->role->level ?? null;
            Log::info('Debug Role Check', [
                'user_id' => $user->id,
                'role_id' => $user->role_id,
                'role_name' => $userRoleName,
                'expected_roles' => $roles
            ]);
            if ($userRoleName && in_array(strtolower($userRoleName), array_map('strtolower', $roles))) {
                return $next($request);
            }
        }
        return response()->view('errors.403', [], 403);
    }
}
