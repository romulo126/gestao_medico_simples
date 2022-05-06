<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\Medicos\medicoService;
use App\Http\Services\Medicos\medicoValidadorService;

class MedicosController extends Controller
{
    public function __construct(medicoService $medicoService, medicoValidadorService $medicoValidadorService)
    {
        $this->medicoService = $medicoService;
        $this->medicoValidadorService = $medicoValidadorService;
    }

    public function index()
    {
        return '';
    }
    
    public function create(Request $request)
    {
      $erro = $this->medicoValidadorService->validateRequestNew($request->all());
      if($erro)
        return response()->json(['error'=>$erro],400);
      $this->medicoService->create($request->all());
      return response()->json(['success'=>'Médico cadastrado com sucesso!'],200);
    }

    public function show(string $type,string $search)
    {
      $date =  $this->medicoService->show($type,$search);
      if(count($date) == 0)
        return response()->json(['error'=>'Nenhum médico encontrado!'],404);
      return response()->json(['success'=>$date],200);
    }

    public function update(Request $request, string $type, int $id)
    {
      $erro = $this->medicoValidadorService->validateRequestUpdate($request->all(),$type);
      if($erro)
        return response()->json(['error'=>$erro],400);
      if($this->medicoService->update($request->all(),$type,$id))
        return response()->json(['success'=>'Médico atualizado com sucesso!'],200);
      return response()->json(['error'=>'Médico não encontrado!'],404);
      
    }

    public function destroy(Request $request,int $id)
    {
      if($this->medicoValidadorService->validateIssetEspecialidade($request->all())){
        $data = $this->medicoService->delete($request->all(),'especialidade',$id);
        if(!$data)
          return response()->json(['success'=>'Especialidade removida com sucesso!'],200);
        return response()->json($data,404);
      }
      $data = $this->medicoService->delete($request->all(),false,$id);
      if(!$data)
        return response()->json(['success'=>'Médico removido com sucesso!'],200);
      return response()->json($data,404);
      
    }
    


}
