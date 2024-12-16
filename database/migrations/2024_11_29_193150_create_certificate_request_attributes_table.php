<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('certificate_request_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_request_id')
                    ->constrained('certificate_requests')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->string('attribute_name');
            $table->string('attribute_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_request_attributes');
    }
};
