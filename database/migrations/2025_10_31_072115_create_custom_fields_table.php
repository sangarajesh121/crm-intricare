<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('field_name', 150); // label
            $table->string('field_key', 150)->unique(); 
            $table->enum('field_type', ['text', 'number', 'date', 'file'])->default('text');
            // $table->enum('field_origin', ['self', 'overrided', 'merged'])->default('self');  //self means field created by user, overrided means field overridden by secondary contact, merged means field transferred from secondary contact
            // $table->json('options')->nullable(); // for select/checkbox options
            // $table->boolean('is_required')->default(false);
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
