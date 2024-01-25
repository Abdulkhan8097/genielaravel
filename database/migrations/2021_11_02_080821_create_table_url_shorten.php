<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUrlShorten extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_shorten', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('url',255)->nullable()->comment('original url');
            $table->string('short_url_domain',255)->nullable()->comment('domain for short url');
            $table->string('short_code',55)->nullable()->comment('domain for short url');
            $table->string('short_url_description',100)->nullable()->comment('short url description');
            $table->integer('hits')->default(0)->nullable()->comment('number of hits of the url');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('url_shorten');
    }
}
