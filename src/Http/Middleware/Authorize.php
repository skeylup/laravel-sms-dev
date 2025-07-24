<?php

namespace Skeylup\LaravelSmsDev\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Authorize
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        // Only apply authorization in production environment
        if (!app()->environment('production')) {
            return $next($request);
        }

        // Check if SMS Dev is enabled
        if (!config('sms-dev.enabled', false)) {
            abort(404);
        }

        // Get the gate name from config
        $gate = config('sms-dev.gate', 'ViewSms');

        // Check if gate exists and user is authorized
        if (Gate::has($gate)) {
            if (Gate::allows($gate, $request->user())) {
                return $next($request);
            }
        } else {
            // If no gate is defined, deny access in production
            abort(403, 'SMS Dev access denied. Please define the authorization gate.');
        }

        // Deny access
        abort(403, 'Unauthorized access to SMS Dev.');
    }
}
