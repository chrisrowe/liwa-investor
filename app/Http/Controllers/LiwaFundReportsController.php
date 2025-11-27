<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileFolder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Storage;
use Auth;
use App\Http\Requests\StoreResearchRequest;
use App\Services\PaginationService;

class LiwaFundReportsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreResearchRequest $request, FileFolder $fileFolder = null)
    {
        $path = Storage::putFile('other', $request->file);

        if (!$path) {
            return;
        }

        $fileDbData = [];
        $fileDbData['name'] = $request->file->getClientOriginalName();
        $fileDbData['path'] = $path;
        $fileDbData['type'] = 'other';
        $fileDbData['created_by'] = Auth::id();
        $fileDbData['extension'] = $request->file->getClientOriginalExtension();

        $file = File::create($fileDbData);

        if ($fileFolder) {
            $fileFolder->files()->attach($file);

            return \Redirect::route('liwa-fund-reports.folder.index', [$fileFolder->id]);
        }

        return redirect()->back();
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
