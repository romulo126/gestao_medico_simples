<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class especialidade extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['nome','descricao'];
    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];
    protected $table = 'especialidade';
}
