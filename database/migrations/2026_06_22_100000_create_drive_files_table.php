<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('drive-files.table_name', 'drive_files');

        Schema::create($table, function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('name');
            $t->string('google_file_id', 255)->unique();
            $t->string('google_drive_folder_id', 255)->nullable();
            $t->string('mime_type', 191)->nullable();
            $t->unsignedBigInteger('size_bytes')->nullable();
            $t->string('visibility', 16)->default('private');
            $t->string('public_link', 1024)->nullable();
            $t->string('category', 64)->nullable();

            // The user who owns this file (their OAuth token will be used
            // to call Google Drive APIs for preview/download/delete/share).
            $t->unsignedBigInteger('uploaded_by_user_id')->nullable();

            $t->json('meta')->nullable();
            $t->timestamps();
            $t->softDeletes();

            $t->index('category');
            $t->index('uploaded_by_user_id');
            $t->index('visibility');
        });
    }

    public function down(): void
    {
        $table = config('drive-files.table_name', 'drive_files');
        Schema::dropIfExists($table);
    }
};
