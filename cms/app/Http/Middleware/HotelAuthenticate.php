<?php

namespace App\Http\Middleware;

use App\Services\PolarisAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HotelAuthenticate
{
    public function __construct(private PolarisAuthService $auth) {}

    public function handle(Request $request, Closure $next): Response
    {
        $id = (int) $request->session()->get('hotel.user_id', 0);
        if ($id < 1) {
            return redirect('/');
        }

        $user = $this->auth->findById($id);
        if (! $user) {
            $request->session()->forget('hotel.user_id');

            return redirect('/');
        }

        $request->attributes->set('hotelUser', $user);
        view()->share('hotelUser', $user);

        return $next($request);
    }
}
