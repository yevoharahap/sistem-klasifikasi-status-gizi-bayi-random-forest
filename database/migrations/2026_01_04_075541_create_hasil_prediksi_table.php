<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('hasil_prediksi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_prediksi');
            $table->string('model');
            $table->string('kelas');
            $table->json('input_data');   // simpan input lengkap
            $table->json('probabilitas'); // simpan probabilitas lengkap
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_prediksi');
    }
};
