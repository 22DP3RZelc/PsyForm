<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->string('type')->default('text');
            $table->unsignedBigInteger('test_id')->nullable();
            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('tests')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
