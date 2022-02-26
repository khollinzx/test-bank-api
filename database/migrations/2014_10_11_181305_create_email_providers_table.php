<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_providers', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("class");
            $table->tinyInteger("is_active")->default(0);
            $table->timestamps();
        });

        (new \App\Models\EmailProvider())->createInitializeNewServiceProviders();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_providers');
    }
}
