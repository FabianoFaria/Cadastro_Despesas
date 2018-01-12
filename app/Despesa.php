<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Despesa extends Model
{
    //
	protected $table        = 'despesas_atendimento';

    protected $primaryKey   = 'Despesa_ID';

    public $timestamps      = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'despesa', 'quantidade', 'valor','observacao', 'Situacao_ID', 'Usuario_Cadastro_ID', 'Origem_Despesa'
    ];

}
