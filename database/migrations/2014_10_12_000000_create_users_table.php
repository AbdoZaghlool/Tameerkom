<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->enum('type', ['0','1'])->default(0);
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('api_token')->nullable();
            $table->string('commercial_record')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('blocked')->default(0);
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('verification_code')->nullable();
            $table->string('image')->default('default.png');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->string('password');
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
        Schema::dropIfExists('users');
    }
}