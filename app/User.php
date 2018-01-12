<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable 
{
    use Notifiable;

    protected $table        = 'cadastros_dados';

    protected $primaryKey   = 'Cadastro_ID';

    public $timestamps      = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Nome', 'Email', 'Senha', 'Tipo_Pessoa', 'Sexo', 'Usuario_Cadastro_ID', 'Situacao_ID'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    

    public $errors;

    public function getAuthPassword() {

        //Indica qual a coluna de senha que a Classe Eloquent irá utilizar para a validação de login
        return $this->Senha;
    }

    public function getReminderEmail() {

        //Indica qual a coluna de senha que a Classe Eloquent irá utilizar para a validação de login
        return $this->Email;
    }


    public function getRememberToken()
    {
       return null; // not supported
    }

    public function setRememberToken($value)
    {
        // not supported
    }

    public function getRememberTokenName()
    {
        return null; // not supported
    }

    /**
    * Overrides the method to ignore the remember token.
    */
    public function setAttribute($key, $value)
    {
        $isRememberTokenAttribute = $key == $this->getRememberTokenName();
        if (!$isRememberTokenAttribute)
        {
            parent::setAttribute($key, $value);
        }
    }
}
