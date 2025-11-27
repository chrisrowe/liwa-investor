<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

use Auth;

class CanSeeFolder
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
    	$user = Auth::user();
        $folderName = $request->route('folder');

        if (!$user->haveAccessToFolder($folderName)) {
            abort(403);
        }

        return $next($request);
    }
}
