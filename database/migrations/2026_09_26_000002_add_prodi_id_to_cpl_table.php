<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProdiIdToCplTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('cpl', function (Blueprint $table) {
            $table->unsignedBigInteger('prodi_id')->nullable()->after('id');

            $table->foreign('prodi_id')
                  ->references('id')
                  ->on('prodi')
                  ->onDelete('set null');

            $table->index('prodi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('cpl', function (Blueprint $table) {
            $table->dropForeign(['prodi_id']);
            $table->dropIndex(['prodi_id']);
            $table->dropColumn('prodi_id');
        });
    }
}
