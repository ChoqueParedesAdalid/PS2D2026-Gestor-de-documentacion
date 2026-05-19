<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 'inscripciones';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'estudiante_id',
        'materia_id',
        'docente_cargo_id',
        'titulo_proyecto',
        'estado_inscripcion',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id', 'id');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id', 'id');
    }

    public function docenteCargo()
    {
        return $this->belongsTo(User::class, 'docente_cargo_id', 'id');
    }

    public function asignacionTutor()
    {
        return $this->hasOne(AsignacionTutor::class, 'inscripcion_id', 'id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'estudiante_id', 'estudiante_id');
    }
    // Agrega esto dentro de la clase Inscripcion:

public function paralelo()
{
    return $this->belongsTo(Paralelo::class, 'paralelo_id');
}
}