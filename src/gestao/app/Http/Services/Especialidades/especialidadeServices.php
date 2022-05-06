<?php
namespace App\Http\Services\Especialidades;
use App\Models\conexao_medico_especialidade;
use App\Models\especialidade;

class especialidadeServices
{
    public function __construct(conexao_medico_especialidade $conexao_medico_especialidade, especialidade $especialidade)
    {
       $this->conexao_medico_especialidade = $conexao_medico_especialidade;
       $this->especialidade = $especialidade;
    }

    public function getEspecialidades($id)
    {
        $especialidades = $this->conexao_medico_especialidade->where('medico_id',$id)->get();
        $especialidades = collect([$especialidades,$this->especialidade])->map(
            function($especialidades,$especialidadeModels){
                $especialidades->nome = $especialidadeModels->where('id',$especialidades->especialidade_id)->first();
                return $especialidades;
            }
        );

      return $especialidades;
    }
}