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
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->string('party_name');
            $table->string('mobile_number')->nullable();
            $table->text('map_link')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('party_type')->default('new')->comment('new, old');
            $table->string('priority_status')->default('non_priority')->comment('priority, non_priority');
            $table->softDeletes();
            $table->timestamps();

            $table->index('party_name');
            $table->index('mobile_number');
            $table->index('party_type');
            $table->index('priority_status');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parties');
    }
};
