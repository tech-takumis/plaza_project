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
        Schema::create('certificate_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')
                    ->constrained('certificates')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->string('placeholder');
            $table->string('data_type')->default('text');
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_attributes');
    }
};
