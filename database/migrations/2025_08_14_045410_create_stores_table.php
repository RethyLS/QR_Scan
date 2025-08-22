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
    Schema::create('stores', function (Blueprint $table) {
        $table->id();
        $table->string('stall_id')->unique();
        $table->string('name');
        $table->string('owner');
        $table->string('group')->nullable();
        $table->double('default_amount')->default(0);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
