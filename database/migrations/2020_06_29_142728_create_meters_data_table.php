<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetersDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meters_data', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->bigInteger('area_id')->unsigned()->index();
            $table->float('water', 10, 2);
            $table->float('last_electricity_day', 10, 2);
            $table->float('last_electricity_night', 10, 2);
            $table->float('electricity_day', 10, 2);
            $table->float('electricity_night', 10, 2);
            $table->timestamp('paid_at')->nullable();
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
        Schema::dropIfExists('meters_data');
    }
}
