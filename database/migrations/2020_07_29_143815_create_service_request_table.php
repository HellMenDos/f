<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('area_id')->unsigned()->index();
            $table->text('description')->nullable();
            $table->text('text_free')->nullable();
            $table->string('execution_date', 191)->nullable();
            $table->integer('execution_hours')->unsigned()->default(0);
            $table->integer('quantity')->unsigned()->default(1);
            $table->tinyInteger('status')->unsigned()->default(0);
            $table->tinyInteger('rating')->unsigned()->default(0);
            $table->bigInteger('service_id')->unsigned()->index();
            $table->text('planning');
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
        Schema::dropIfExists('service_requests');
    }
}
