<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\DatabaseSwitcher;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SwitchDatabaseConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $role = $this->determineUserRole($user);

        try {
            DatabaseSwitcher::switchConnection($role);
            Log::info("Switched to $role database connection for route: " . $request->path());

            return $next($request);
        } catch (\Exception $e) {
            Log::error("Failed to switch database connection: " . $e->getMessage(), [
                'role' => $role,
                'route' => $request->path(),
                'exception' => $e
            ]);

            return response()->json(['error' => 'Database connection failed'], 500);
        }
    }

    /**
     * Determine the user's role.
     *
     * @param  mixed  $user
     * @return string
     */
    private function determineUserRole($user): string
    {
        if ($user instanceof \App\Models\Staff) {
            return $user->is_admin ? 'admin' : 'staff';
        }
        return 'user';
    }
}
