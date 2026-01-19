<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('child_dedications', function (Blueprint $table) {
            $table->id();
            $table->string('child_firstname');
            $table->string('child_middlename')->nullable();
            $table->string('child_lastname');
            $table->date('child_dob');
            $table->string('child_pob');
            $table->string('child_gender');
            $table->string('father_fullname')->nullable();
            $table->string('mother_fullname')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->date('date_of_dedication')->nullable();
            $table->string('venue');
            $table->string('officiating_pastor');
            $table->json('sponsors');
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
        Schema::dropIfExists('child_dedications');
    }
};
