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
        // Kiểm tra nếu chưa đăng nhập
        if (!Auth::check() && !Auth::guard('admin')->check()) {
            return redirect()->route('login');
        }

        // Kiểm tra guard 'web' (người dùng thông thường)
        if (Auth::guard('web')->check()) {
            // Người dùng thông thường chỉ được phép nếu $roles chứa 'user'
            if (in_array('user', $roles)) {
                return $next($request);
            }
            return redirect('/404');
        }

        // Kiểm tra guard 'admin' (quản trị viên)
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            // Kiểm tra nếu vai trò của admin nằm trong danh sách $roles
            if (in_array($admin->role, $roles)) {
                return $next($request);
            }
            return redirect('/404');
        }

        return redirect()->route('login');
    }
}
