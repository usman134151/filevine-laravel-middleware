<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {   
        Schema::rename('user_a_p_i_lists', 'user_api_list');
        Schema::table('user_api_list', function (Blueprint $table) {
            $table->string('filevine_url')->default('https://sandbox.api.filevineapp.com/');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_api_list', function (Blueprint $table) {
            $table->dropColumn('filevine_url');
        });
        
        Schema::dropIfExists('user_api_list'); // new add for drop table
    }
    
};
