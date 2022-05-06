<?php

namespace App\Helper;

class menssageErrorHelper
{
    private $error = false;

    public function setMenssageError(string $rule, string $message)
    {
       $this->error[$rule][] = $message;
       return $this->error;
    }

    public function getMenssageError()
    {
       return $this->error;
    }
}