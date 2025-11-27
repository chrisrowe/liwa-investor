<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDashboardToFileFoldersFolderEnumColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE file_folder_actions MODIFY COLUMN folder ENUM('videos', 'research', 'other', 'dashboard')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_folder_actions', function (Blueprint $table) {
            //
        });
    }
}
