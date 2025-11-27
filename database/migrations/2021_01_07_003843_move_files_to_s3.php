<?php

use App\Models\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveFilesToS3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $files = File::all();
        foreach($files as $file){
            if(Storage::disk('local')->exists($file->path)){
                $contents = Storage::disk('local')->get($file->path);
                $storagePath = Storage::disk('s3')->put($file->path, $contents);
                if($storagePath){
                    Storage::disk('local')->delete($file->path);
                }
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
