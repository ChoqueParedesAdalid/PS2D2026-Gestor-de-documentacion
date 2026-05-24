<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materias';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'semestre_requerido',
        'gestion_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relación: Pertenece a una gestión
    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'gestion_id', 'id');
    }

    // Relación: Tiene muchas inscripciones
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'materia_id', 'id');
    }

    // Relación: Tiene muchas tareas
    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'materia_id', 'id');
    }
    public function docenteCargo()
    {
        return $this->belongsTo(User::class, 'docente_cargo_id', 'id');
    }
}