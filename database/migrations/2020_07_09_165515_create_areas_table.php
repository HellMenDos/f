<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('address');
            $table->string('contract_number', 50);
            $table->string('contract_date', 50);
            $table->string('contract_file');
            $table->float('land_area', 8, 2);
            $table->float('house_area', 8, 2);
            $table->integer('quantity_residents')->unsigned();
            $table->text('сounters');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('areas');
    }
}
