<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConexaoMedicoEspecialidade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conexao_medico_especialidade', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medico_id');
            $table->unsignedBigInteger('especialidade_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('medico_id')->references('id')->on('medico');
            $table->foreign('especialidade_id')->references('id')->on('especialidade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conexao_medico_especialidade');
    }
}
