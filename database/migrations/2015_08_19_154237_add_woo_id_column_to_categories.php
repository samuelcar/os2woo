<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWooIdColumnToCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('oscommerce')->table('categories_description', function (Blueprint $table) {
            $table->integer('woo_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('oscommerce')->table('categories_description', function (Blueprint $table) {
           $table->dropColumn('woo_id');
        });
    }
}
