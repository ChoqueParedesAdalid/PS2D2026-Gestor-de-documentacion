<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    protected $table = 'gestiones';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'activa',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activa' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relación: Una gestión tiene muchas materias
    public function materias()
    {
        return $this->hasMany(Materia::class, 'gestion_id', 'id');
    }
}