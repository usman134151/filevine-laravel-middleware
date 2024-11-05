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
        Schema::create('user_a_p_i_lists', function (Blueprint $table) {
            $table->id();
            $table->string('filevine_API_key');
            $table->string('filevine_API_secret');
            $table->string('roadProof_API_key');
            $table->string('client_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_a_p_i_lists');
    }
};
