<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
    {
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropUnique('cars_vin_unique'); // șterge indexul existent
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->string('vin')->nullable()->unique()->change();
        });
    }

    public function down()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropUnique('cars_vin_unique');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->string('vin')->unique()->nullable(false)->change();
        });
    }
};
