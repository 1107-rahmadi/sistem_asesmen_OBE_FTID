<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatriksCpmkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matriks_cpmk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cpmk_id');
            $table->string('kode_mk', 20);
            $table->string('nama_mk_matrix', 150)->nullable();
            $table->decimal('bobot_partisipatif', 6, 2)->nullable()->default(0);
            $table->decimal('bobot_tugas', 6, 2)->nullable()->default(0);
            $table->decimal('bobot_uts', 6, 2)->nullable()->default(0);
            $table->decimal('bobot_uas', 6, 2)->nullable()->default(0);
            $table->decimal('skor_maksimal', 6, 2);
            $table->timestamps();

            $table->foreign('cpmk_id')
                ->references('id')
                ->on('cpmk')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->index('kode_mk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matriks_cpmk');
    }
}
