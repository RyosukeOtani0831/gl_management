<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BasicAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $username = $request->getUser();
        $password = $request->getPassword();
    
        $authUser = DB::table('basic_auth_users')
            ->where('username', $username)
            ->where('is_active', true)
            ->first();
        
        if ($authUser && Hash::check($password, $authUser->password)) {
            return $next($request);
        }
    
        abort(401, "Enter username and password.", [
            header('WWW-Authenticate: Basic realm="Sample Private Page"'),
            header('Content-Type: text/plain; charset=utf-8')
        ]);
    }
}