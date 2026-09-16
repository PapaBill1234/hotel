<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfHotelAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if ((int) $request->session()->get('hotel.user_id', 0) > 0) {
            return redirect('/me');
        }

        return $next($request);
    }
}
