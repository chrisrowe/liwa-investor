<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileFolderActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_folder_actions', function (Blueprint $table) {
            $table->id();
            $table->enum('action', ['visit', 'add_access', 'remove_access']);
            $table->enum('folder', ['videos', 'research', 'other']);
            $table->integer('subfolder_id')->nullable();
            $table->integer('initiated_by');
            $table->json('action_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_folder_actions');
    }
}
