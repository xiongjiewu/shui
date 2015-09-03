<?php

namespace App\Http\Middleware;

use App\Application\User\AuthService;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class AdminCheck
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
        if (!AuthService::check()) {
            return redirect(route('admin::login'));
        }
        return $next($request);
    }
}