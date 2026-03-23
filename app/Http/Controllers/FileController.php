<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileAction;
use Illuminate\Http\Request;
use Storage;
use Config;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function viewFile(File $file)
    {
        $user = Auth::user();
        $subfolder = $file->folder()->first();
        $folderName = $file->type;

        if (!$user->haveAccessToFolder($folderName) || !$user->haveAccessToSubfolder($subfolder)) {
            abort(403);
        }

        if ($user->hasRole(['investor'])) {
            FileAction::create([
                'file_id' => $file->id,
                'type' => 'view',
                'initiated_by' => $user->id,
            ]);
        }
        if ($file->type === 'videos') {
            if (!$file->video) {
                return response()->json([
                    'success' => false,
                ], 404);
            }
            
            return [
                $file->video->embeded
            ];
        }
        
        if (!Storage::exists($file->path)) {
            return response()->json([
                'success' => false,
            ], 404);
        }
        
        return Storage::response($file->path);
    }

    public function downloadFile(File $file)
    {
        $user = Auth::user();
        $subfolder = $file->folder()->first();
        $folderName = $file->type;

        if (!$file->path) {
            return response()->json([
                'success' => false,
            ], 404);
        }

        if (!$user->haveAccessToFolder($folderName) || !$user->haveAccessToSubfolder($subfolder)) {
            abort(403);
        }

        if ($user->hasRole(['investor'])) {
            FileAction::create([
                'file_id' => $file->id,
                'type' => 'download',
                'initiated_by' => $user->id,
            ]);
        }
        if (!Storage::exists($file->path)) {
            return response()->json([
                'success' => false,
            ], 404);
        }
        
        return Storage::download($file->path);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        //
    }
}
