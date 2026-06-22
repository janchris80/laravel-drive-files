<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('drive-files.tokens_table_name', 'drive_tokens');

        Schema::create($table, function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->text('access_token');
            $t->text('refresh_token')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->string('scope', 1024)->nullable();
            $t->string('token_type', 32)->nullable()->default('Bearer');
            $t->string('connected_email', 191)->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        $table = config('drive-files.tokens_table_name', 'drive_tokens');
        Schema::dropIfExists($table);
    }
};
