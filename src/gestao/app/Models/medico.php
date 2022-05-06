<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class medico extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = ['nome','crm','telefoneFixo','telefoneCelular','cep'];
    protected $guarded = ['id'];
    protected $table = 'medico';
    protected $dates = ['deleted_at'];
}
