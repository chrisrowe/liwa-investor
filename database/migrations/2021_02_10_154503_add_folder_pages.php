<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Page;

class AddFolderPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $baseFileFolders = config('filefolders.baseFolders');
        foreach ($baseFileFolders as $baseFileFolder) {
            $page = new Page;
            if ($baseFileFolder === 'liwa-fund-reports') {
                $baseFileFolder = 'other';
            }
            $page->name = $baseFileFolder;
            $page->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
