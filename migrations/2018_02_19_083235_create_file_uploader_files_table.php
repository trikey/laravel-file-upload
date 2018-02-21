<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileUploaderFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_uploader_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('public_id')->unique();
            $table->string('format');
            $table->string('mime_type');
            $table->bigInteger('bytes');
            $table->string('path');
            $table->string('disk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_uploader_files');
    }
}
