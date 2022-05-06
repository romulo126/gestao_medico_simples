<?php

namespace App\Http\Services\Cep;


class cepService
{


    public function getAddressViaCep($cep)
   {
      $url = "https://viacep.com.br/ws/$cep/json/";
      try
      {
         $json = file_get_contents($url);
         $dateCep = json_decode($json,true);
         $dateCep = [
            'cep' => $dateCep['cep'],
            'rua' => $dateCep['logradouro'],
            'bairro' => $dateCep['bairro'],
            'cidade' => $dateCep['localidade'],
            'complemento' => $dateCep['complemento'],
            'uf' => $dateCep['uf']
         ];
      }
      catch(\Exception $e)
      {
         return false;
      }
      return $dateCep;
   }
}