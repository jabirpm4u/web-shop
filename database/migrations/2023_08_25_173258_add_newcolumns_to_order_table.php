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
        Schema::table('orders', function (Blueprint $table) {

            Schema::table('orders', function($table) {
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->integer('product_count')->default(0); 
            });
               
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('total_amount');
            $table->dropColumn('product_count');
        });
    }
};
