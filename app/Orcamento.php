<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
    //
    protected $table        = 'orcamentos_workflows';

    protected $primaryKey   = 'Workflow_ID';

    public $timestamps      = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable 	= [];
}
