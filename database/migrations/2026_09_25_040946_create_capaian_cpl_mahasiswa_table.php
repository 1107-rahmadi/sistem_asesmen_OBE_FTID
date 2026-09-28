<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCapaianCplMahasiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capaian_cpl_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('nim', 20);
            $table->unsignedBigInteger('cpl_id');
            $table->decimal('persen_capaian', 5, 2);
            $table->unsignedInteger('jumlah_mk_dipakai')->default(0);
            $table->boolean('status_tercapai')->default(false);
            $table->timestamp('dihitung_pada')->nullable();
            $table->timestamps();

            $table->foreign('nim')
                ->references('nim')
                ->on('mahasiswa')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('cpl_id')
                ->references('id')
                ->on('cpl')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unique(['nim', 'cpl_id'], 'uq_nim_cpl');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('capaian_cpl_mahasiswa');
    }
}
