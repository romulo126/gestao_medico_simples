<?php

namespace App\Http\Services\Medicos;
use App\Helper\menssageErrorHelper;
use App\Http\Services\Cep\cepService;
use App\Models\especialidade;

class medicoValidadorService
{

    private $error = false;
    private $request;

    public function __construct(menssageErrorHelper $menssageErrorHelper,cepService $cepService,especialidade $especialidade)
    {
        $this->menssageErrorHelper = $menssageErrorHelper;
        $this->cepService = $cepService;
        $this->especialidade = $especialidade;
    }

    public function validateIssetEspecialidade(array $request)
    {
         $this->request = $request;
         if(isset($request['especialidade']))
         {
            return true;
         }
         return false;
    }

    public function validateRequestUpdate(array $request,string $type)
    {
         $this->request = $request;
         $this->validateRequestRequerid([$type]);
         if($this->menssageErrorHelper->getMenssageError())
            return $this->menssageErrorHelper->getMenssageError();

         if ($type == 'nome') {
            $this->validateRequestSTRLEN(['nome',],['nome'=>['min'=>3,'max'=>120]]);
         }elseif ($type == 'crm') {
            $this->validateRequestSTRLEN(['crm',],['crm'=>['min'=>1,'max'=>7]]);
         }elseif ($type == 'telefoneFixo') {
            $this->validateRequestSTRLEN(['telefoneFixo'],['telefoneFixo'=>['min'=>8,'max'=>11]]);
         }elseif ($type == 'telefoneCelular') {
            $this->validateRequestRequerid([$type]);
            $this->validateRequestSTRLEN(['telefoneCelular'],['telefoneCelular'=>['min'=>8,'max'=>11]]);
         }elseif ($type == 'cep') {
            $this->validateRequestSTRLEN(['cep'],['cep'=>['min'=>8,'max'=>8]]);
            $this->validateRequestCep();
         }elseif ($type == 'especialidade') {
            $this->validateRequestEspecialidade();
         }

         return $this->menssageErrorHelper->getMenssageError();
    }

    public function validateRequestNew(array $request)
    {
 
       $this->request = $request;
       $rules = ['nome','crm','especialidade','telefoneFixo','telefoneCelular','cep'];
       $this->validateRequestRequerid($rules);
       
       if($this->menssageErrorHelper->getMenssageError())
         return $this->menssageErrorHelper->getMenssageError();
 
       $this->validateRequestNumeric(['crm','telefoneFixo','telefoneCelular','cep']);
       $this->validateRequestIsArray(['especialidade']);
       $this->validateRequestMinInArray(['especialidade'],['especialidade'=>2]);
       $this->validateRequestEspecialidadeInDatabase();
       $this->validateRequestCep();
       $this->validateRequestSTRLEN(['nome','crm','telefoneFixo','telefoneCelular','cep'],['nome'=>['min'=>3,'max'=>120],
                                            'crm'=>['min'=>1,'max'=>7],
                                            'telefoneFixo'=>['min'=>8,'max'=>20],
                                            'telefoneCelular'=>['min'=>8,'max'=>15],
                                            'cep'=>['min'=>8,'max'=>15]]);
       
       return $this->menssageErrorHelper->getMenssageError();
    }
 
   private function validateRequestEspecialidade()
   {
      if(is_array($this->request['especialidade']))
      {
         foreach ($this->request['especialidade'] as $key => $especialidade) {
            if(is_numeric($key))
            {
               if(!$this->especialidade->where('nome',$especialidade)->first())
               {
                  $this->menssageErrorHelper->setMenssageError('update','A especialidade '.$especialidade.' Não Existe');
               }
            }else{
               if($key != 'new' and !$this->especialidade->where('nome',$key)->first())
               {
                  $this->menssageErrorHelper->setMenssageError('update','A especialidade '.$key.' Não Existe');
               }elseif($key == 'new' and is_array($especialidade)){
                  foreach ($especialidade as  $especialidadeNew) {
                     if(!$this->especialidade->where('nome',$especialidadeNew)->first())
                     {
                        $this->menssageErrorHelper->setMenssageError('update','A especialidade '.$especialidadeNew.' Não Existe');
                     }
                  }
               }else{
                  if(!$this->especialidade->where('nome',$especialidade)->first())
                  {
                     $this->menssageErrorHelper->setMenssageError('update','A especialidade '.$especialidade.' Não Existe');
                  }
               }
            }
         }
      }else{
         $especialidade = $this->especialidade->where('nome',$this->request['especialidade'])->first();
         if(!$especialidade)
         {
            $this->menssageErrorHelper->setMenssageError('update','A especialidade '.$this->request['especialidade'].' Não Existe');
            return;
         }
      }
   }

    private function validateRequestEspecialidadeInDatabase()
   {
      $especialidades = $this->request['especialidade'];

      foreach($especialidades as $especialidade)
      {
         $especialidadedb = $this->especialidade->where('nome',$especialidade)->first();
         if(!$especialidadedb)
         {
            $this->menssageErrorHelper->setMenssageError('especialidade','Especialidade \''.$especialidade.'\' não encontrada');
         }
      }

   }

   private function validateRequestSTRLEN(array $rules, array $minAndMax)
   {
      foreach($rules as $rule)
      {  
         if(strlen($this->request[$rule]) > $minAndMax[$rule]['max'] || strlen($this->request[$rule]) < $minAndMax[$rule]['min'])
         {
            $this->menssageErrorHelper->setMenssageError($rule,$rule.' deve ter entre '.$minAndMax[$rule]['min'].' e '.$minAndMax[$rule]['max'].' caracteres');
         }
      }
   }

   private function validateRequestCep()
   {
        if(!is_numeric($this->request['cep']))
        {
            $this->menssageErrorHelper->setMenssageError('cep','CEP deve ser somente numérico sem caracteres especiais');
        }
        if(strlen($this->request['cep']) != 8)
        {
            $this->menssageErrorHelper->setMenssageError('cep','CEP deve ter 8 caracteres');
        }
      
        $validate = $this->cepService->getAddressViaCep($this->request['cep']);
        if(!$validate)
        {
            $this->menssageErrorHelper->setMenssageError('cep','CEP não encontrado');
        }
   }
   
   private function validateRequestMinInArray(array $rules, array $minimo)
   {
      foreach($rules as $rule)
      {
         if(count($this->request[$rule]) < $minimo[$rule])
         {
            $this->menssageErrorHelper->setMenssageError($rule,'O campo '.$rule.' deve ter no mínimo '.$minimo[$rule].' itens');
         }
      }
      
   }
   
   private function validateRequestIsArray($rules)
   {
      foreach($rules as $rule)
      {
         if(!is_array($this->request[$rule]))
         {
            $this->menssageErrorHelper->setMenssageError($rule,'O campo '.$rule.' deve ser um array');
         }
      }
   }

   private function validateRequestNumeric(array $rulesNumeric)
   {
      foreach ($rulesNumeric as $value) {
         if(!is_numeric($this->request[$value]))
         {
            $this->menssageErrorHelper->setMenssageError($value,'O campo '.$value.' deve ser numérico sem caracteres especiais');
         }
      }
      
   }

   private function validateRequestRequerid(array $rulesAll)
   {
      foreach($rulesAll as $value)
      {
         if(!isset($this->request[$value])|| $this->request[$value] == null)
         {
            $this->menssageErrorHelper->setMenssageError($value,'O campo '.$value.' é requerido');
         }
      }
   }
}