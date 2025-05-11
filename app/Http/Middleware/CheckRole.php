<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (in_array($user->role, $roles)) {
                return $next($request);
            }
        }

        // Nếu không có quyền truy cập, chuyển hướng đến trang 403
        return response()->view('errors.403', [], 403);
    }
}
