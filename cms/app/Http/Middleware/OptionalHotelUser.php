<?php

namespace App\Http\Middleware;

use App\Services\PolarisAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptionalHotelUser
{
    public function __construct(private PolarisAuthService $auth) {}

    public function handle(Request $request, Closure $next): Response
    {
        $id = (int) $request->session()->get('hotel.user_id', 0);
        if ($id > 0) {
            $user = $this->auth->findById($id);
            if ($user) {
                $request->attributes->set('hotelUser', $user);
                view()->share('hotelUser', $user);
            }
        }

        return $next($request);
    }
}
