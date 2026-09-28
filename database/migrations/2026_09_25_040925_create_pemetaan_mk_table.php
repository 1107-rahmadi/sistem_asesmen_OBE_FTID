<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemetaanMkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pemetaan_mk', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mk_siakad', 150)->unique();
            $table->string('kode_mk', 20)->nullable();
            $table->boolean('status_terverifikasi')->default(false);
            $table->string('catatan', 255)->nullable();
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
        Schema::dropIfExists('pemetaan_mk');
    }
}
