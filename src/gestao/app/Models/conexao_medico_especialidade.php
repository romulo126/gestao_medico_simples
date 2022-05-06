<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class conexao_medico_especialidade extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['medico_id','especialidade_id'];
    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];
    protected $table = 'conexao_medico_especialidade';
}
