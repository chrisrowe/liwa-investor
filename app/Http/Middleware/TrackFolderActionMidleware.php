<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use App\Models\FileFolderAction;
// use Illuminate\Support\Facades\Auth;

use Auth;

class TrackFolderActionMidleware
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
    	$route = $request->route();
    	$routeName = $route->uri;
        $folderName = '';

    	switch ($routeName) {
            case '/':
                $folderName = 'dashboard';
                break;
    		case '{folder}':
    		case 'folders/{folder}/{file_folder}':
    			$folderName = $request->route('folder');
    			if ($folderName === 'liwa-fund-reports') {
    				$folderName = 'other';
    			}
                break;
    		default:
    			return $next($request);
    	}

        $fileFolderAction = new FileFolderAction;
        $fileFolderAction->action = 'visit';
        $fileFolderAction->folder = $folderName;
        $fileFolderAction->subfolder_id = $request->route('file_folder') ?
            $request->route('file_folder')->id :
            null;
        $fileFolderAction->initiated_by = $user->id;
        $fileFolderAction->action_data = null;
        $fileFolderAction->save();

        return $next($request);
    }
}
