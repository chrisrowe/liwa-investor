<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileFolder;
use App\Models\FileVideo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreVideoRequest;
use App\Services\PaginationService;
use App\Http\Requests\EmbedVideoRequest;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $videosQuery = File::where('type', 'video')
            ->orderBy('id', 'DESC');

        if (!$folder) {
            $videosQuery->hasNot('folder');
        }


        $videos = $videosQuery->paginate(50);
        $paginatedLinks = PaginationService::paginationLinks($videos);

        return Inertia::render('Videos', [
            'videos' => $videos,
            'paginatedLinks' => $paginatedLinks,
        ]);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVideoRequest $request, FileFolder $fileFolder = null)
    {
        $path = Storage::putFile('videos', $request->file);

        if (!$path) {
            return;
        }

        $fileDbData = [];
        $fileDbData['name'] = $request->file->getClientOriginalName();
        $fileDbData['path'] = $path;
        $fileDbData['type'] = 'videos';
        $fileDbData['created_by'] = Auth::id();
        $fileDbData['extension'] = $request->file->getClientOriginalExtension();

        $file = File::create($fileDbData);

        if ($fileFolder) {
            $fileFolder->files()->attach($file);

            return \Redirect::route('videos.folder.index', [$fileFolder->id]);
        }

        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function embed(EmbedVideoRequest $request, FileFolder $fileFolder = null)
    {
        $fileDbData = [];
        $fileDbData['name'] = $request->linkName;
        $fileDbData['path'] = null;
        $fileDbData['type'] = 'videos';
        $fileDbData['created_by'] = Auth::id();
        $fileDbData['extension'] = 'mp4';

        $file = File::create($fileDbData);
        $fileVideo = new FileVideo;
        $fileVideo->file_id = $file->id;
        $fileVideo->embeded = $request->embeded;
        $fileVideo->save();

        if ($fileFolder) {
            $fileFolder->files()->attach($file);
            return \Redirect::route('videos.folder.index', [$fileFolder->id]);
        }

        return redirect()->back();
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
        $file->delete();

        return redirect()->back();
    }
}
