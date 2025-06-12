<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Model
{
    use Notifiable;

    protected $table = 'Usuario';
    protected $primaryKey = 'c_usuario';
    public $timestamps = false;

    protected $fillable = [
        'c_usuario',
        'l_nombre',
        'l_apellido',
        'c_rol',
        'f_nacimiento',
        'l_correoElectronico',
        'l_contrasena',
        'l_fotoPerfil'
    ];

        protected $hidden = [
        'l_contrasena',
    ];

    public function getAuthPassword()
    {
        return $this->l_contrasena; // Debe coincidir con el campo de contraseña en tu tabla
    }
}
