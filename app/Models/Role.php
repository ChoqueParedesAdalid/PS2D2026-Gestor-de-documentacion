<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // Relación inversa: Un rol tiene muchos usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }
}