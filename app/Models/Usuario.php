<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    // Nombre de tabla en la BD
    protected $table = 'Usuario';

    // Clave primaria de tu tabla
    protected $primaryKey = 'c_usuario';

    // Si usas UUID o string como PK → no autoincrementa
    public $incrementing = false;

    // Si no tienes timestamps tipo created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'l_nombre',
        'l_apellido',
        'l_correoElectronico',
        'l_contrasena',
        'f_nacimiento',
        'c_rol',
        'l_fotoPerfil',
    ];

    protected $hidden = [
        'l_contrasena',
    ];

    // Laravel usa esto para saber qué columna es la contraseña
    public function getAuthPassword()
    {
        return $this->l_contrasena;
    }

    // Si tu PK es UUID y no integer, especifica que es string
    protected $keyType = 'string';
}
