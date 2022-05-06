<?php

namespace App\Http\Services\Medicos;
use App\Models\medico;
use App\Models\conexao_medico_especialidade;
use App\Models\especialidade;
use App\Http\Services\Cep\cepService;

class medicoService
{
   

   public function __construct(conexao_medico_especialidade $conexao_medico_especialidade,
                               medico $medico,especialidade $especialidade,cepService $cepService)
   {
      $this->conexao_medico_especialidade = $conexao_medico_especialidade;
      $this->medico = $medico;
      $this->especialidade = $especialidade;
      $this->cepService = $cepService;
   }

   public function create(array $request)
   {
      $this->request = $request;
      $this->medico->nome = $request['nome'];
      $this->medico->crm = $request['crm'];
      $this->medico->telefoneFixo = $request['telefoneFixo'];
      $this->medico->telefoneCelular = $request['telefoneCelular'];
      $this->medico->cep = $request['cep'];
      $this->medico->save();

      foreach ($request['especialidade'] as $especialidade) {
         $id = $this->especialidade->where('nome',$especialidade)->first()->id;
         conexao_medico_especialidade::create([
            'medico_id' => $this->medico->id,
            'especialidade_id' => $id
         ]);
      }
   }

   public function show(string $type,string $search)
   {
      if($type == 'nome')
         $medicos = $this->medico->where('nome','like','%'.$search.'%')->get();
      elseif($type == 'crm')
         $medicos = $this->medico->where('crm','=',$search)->get();
      elseif($type == 'telefone')
         $medicos = $this->medico->where('telefoneFixo','=',$search)->orWhere('telefoneCelular','like','%'.$search.'%')->get();
      elseif($type == 'especialidade'){
         $medicos = $this->especialidade->where('nome','=',$search)->get(['nome','id']);
         $medicos = $this->conexao_medico_especialidade->whereIn('especialidade_id',$medicos->pluck('id'))->get(['medico_id']);
         $medicos = $this->medico->whereIn('id',$medicos->pluck('medico_id'))->get();
      }elseif($type == 'cep')
         $medicos = $this->medico->where('cep','=',$search)->get();
      else
         $medicos = $this->medico->all();
      
      
      $medicos = $medicos->map(function($medico){
         $medico->especialidades = $this->conexao_medico_especialidade->where('medico_id',$medico->id)->get('especialidade_id');
         $medico->especialidades = $this->especialidade->whereIn('id',$medico->especialidades)->get('nome')->pluck('nome');
         $medico->cep = $this->cepService->getAddressViaCep($medico->cep);
         return $medico;
      });

      return $medicos;
   }

   public function update(array $request,string $type,int $id)
   {
      if($type == 'nome')
         $this->medico->where('id',$id)->update(['nome' => $request['nome']]);
      elseif($type == 'crm')
         $this->medico->where('id',$id)->update(['crm' => $request['crm']]);
      elseif($type == 'telefoneFixo')
         $this->medico->where('id',$id)->update(['telefoneFixo' => $request['telefoneFixo']]);
      elseif($type == 'telefoneCelular')
         $this->medico->where('id',$id)->update(['telefoneCelular' => $request['telefoneCelular']]);
      elseif($type == 'cep')
         $this->medico->where('id',$id)->update(['cep' => $request['cep']]);
      elseif($type == 'especialidade'){
         $this->updateEspecialidade($request,$id);
      }else{
         return false;
      }
      return true;

   }

   public function delete(array $request=[], string $type,int $id)
   {
      if($type == 'especialidade'){
         if(is_array($request['especialidade'])){
            foreach($request['especialidade'] as $especialidade){
               $data = $this->especialidade->where('nome',$especialidade)->first();
               if(!isset($data->id)){
                  return ['error' => 'A especialidade '.$especialidade.' para o id do medico informado não foi encontrado'];
               }
               if(!$this->conexao_medico_especialidade->where('especialidade_id',$data->id)->where('medico_id',$id)->delete())
                  return ['error' => 'Erro ao deletar verifique o id do médico'];
            }
            return false;
         }else{
            $data =  $this->especialidade->where('nome',$request['especialidade'])->first();
            if(!isset($data->id)){
               return ['error' => 'A especialidade '.$request['especialidade'].' para o id do medico informado não foi encontrado'];
            }
            if($data->id){
               if($this->conexao_medico_especialidade->where('especialidade_id',$data->id)->where('medico_id',$id)->delete())
                  return false;
               return ['error' => 'Erro ao deletar verifique o id do médico'];
            }
            return ['error' => 'A especialidade '.$request['especialidade'].' para o id do medico informado não foi encontrado'];

         }
      }else{
         $this->conexao_medico_especialidade->where('medico_id',$id)->delete();
         if($this->medico->where('id',$id)->delete())
            return false;
         
         return ['error' => 'Erro ao deletar verifique o id do médico'];
      }
   }

   private function updateEspecialidade(array $request,int $id)
   {
      if(is_array($request['especialidade'])){
         $controlerDelet=0;
         
         foreach ($request['especialidade'] as $key => $especialidade) {
            if(is_numeric($key)){
               if($controlerDelet == 0)
               {
                  $this->conexao_medico_especialidade->where('medico_id',$id)->delete();
                  $controlerDelet = 1;
               }
               $id_especialidade = $this->especialidade->where('nome',$especialidade)->first()->id;
               conexao_medico_especialidade::create([
                  'medico_id' => $id,
                  'especialidade_id' => $id_especialidade
               ]);
            }else{
               if($key != 'new'){
                  $id_especialidade_old = $this->especialidade->where('nome',$key)->first()->id;
                  $id_especialidade_new = $this->especialidade->where('nome',$especialidade)->first()->id;
                  $this->conexao_medico_especialidade->where('medico_id',$id)
                  ->where('especialidade_id',$id_especialidade_old)
                  ->update(['especialidade_id' => $id_especialidade_new]);
               }else{
                  if(is_array($especialidade)){
                     foreach($especialidade as $especialidadeNew){
                        $id_especialidade = $this->especialidade->where('nome',$especialidadeNew)->first()->id;
                        conexao_medico_especialidade::create([
                           'medico_id' => $id,
                           'especialidade_id' => $id_especialidade
                        ]);
                     }
                  }else{
                     $id_especialidade = $this->especialidade->where('nome',$especialidade)->first()->id;
                     if(count($this->conexao_medico_especialidade->where('medico_id',$id)
                     ->where('especialidade_id',$id_especialidade)->get()) == 0){
                        conexao_medico_especialidade::create([
                           'medico_id' => $id,
                           'especialidade_id' => $id_especialidade
                        ]);
                     }
                  }
               }
            }
         }
      }else{
         $this->conexao_medico_especialidade->where('medico_id',$id)->delete();
         $id_especialidade = $this->especialidade->where('nome',$request['especialidade'])->first()->id;
         conexao_medico_especialidade::create([
            'medico_id' => $id,
            'especialidade_id' => $id_especialidade
         ]);
      }
   

   }

   

}