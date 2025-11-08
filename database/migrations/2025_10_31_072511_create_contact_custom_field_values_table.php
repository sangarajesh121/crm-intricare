<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contact_custom_field_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contact_id')->index();
            $table->unsignedBigInteger('custom_field_id')->index();
            $table->text('field_value')->nullable(); 
            $table->boolean('is_active')->default(true);
            $table->enum('field_origin', ['self', 'overrided', 'merged'])->default('self');  //self means field created by user, overrided means field overridden by secondary contact, merged means field transferred from secondary contact
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('custom_field_id')->references('id')->on('custom_fields')->onDelete('cascade');

            $table->unique(['contact_id', 'custom_field_id'], 'contact_custom_field_unique');
        });
    }

    public function down(): void
    {
        Schema::table('contact_custom_field_values', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
            $table->dropForeign(['custom_field_id']);
        });
        Schema::dropIfExists('contact_custom_field_values');
    }
};
