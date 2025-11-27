<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use App\Models\User;

use Auth;

class CanSeeSubfolder
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
    	$subfolder = $request->route('file_folder');

    	if (!$user->haveAccessToSubfolder($subfolder)) {
    		abort(403);
    	}

        return $next($request);
    }
}
