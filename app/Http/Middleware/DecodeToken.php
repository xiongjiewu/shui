<?php

namespace App\Http\Middleware;

use App\Application\User\AuthService;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class DecodeToken
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        AuthService::setUserByToken($request->cookie('admin_token'));
        return $next($request);
    }
}