<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->nullable();
            $table->string('email', 255)->nullable()->index();
            $table->string('phone', 50)->nullable()->index();
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->index();
            $table->string('profile_image_path', 255)->nullable();
            $table->string('other_doc', 255)->nullable();
            $table->enum('status', ['active', 'inactive', 'merged'])->default('active')->index();
            $table->unsignedBigInteger('merged_into')->nullable()->index();
            $table->timestamps();

            
            $table->foreign('merged_into')->references('id')->on('contacts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['merged_into']);
        });
        Schema::dropIfExists('contacts');
    }
};
