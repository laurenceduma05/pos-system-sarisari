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
        Schema::create('teachings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploader')->constrained('users')->onDelete('cascade'); // Assuming 'users' table for uploader
            $table->string('title');
            $table->string('descriptions')->nullable();
            $table->string('link')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->json('audience')->nullable(); // JSON field for storing audience types (e.g., junior pastor, youth leader, members)
            $table->boolean('archive')->default(false); // Boolean to store archived status
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
        Schema::dropIfExists('teachings');
    }
};
