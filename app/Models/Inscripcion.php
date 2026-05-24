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
        'estado_inscripcion' => 'string',
    ];

    // ==================================================
    // RELACIONES (ESTO ES LO QUE FALTABA)
    // ==================================================

    /**
     * Relación: Pertenece a un Estudiante
     */
    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id', 'id');
    }

    /**
     * Relación: Pertenece a una Materia
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id', 'id');
    }

    /**
     * Relación: Pertenece a un Docente a Cargo
     */
    public function docenteCargo()
    {
        return $this->belongsTo(User::class, 'docente_cargo_id', 'id');
    }

    /**
     * Relación: Tiene un Tutor (a través de asignaciones_tutor)
     */
    public function tutores()
    {
        return $this->belongsToMany(User::class, 'asignaciones_tutor', 'inscripcion_id', 'tutor_id')
                    ->wherePivot('activo', true)
                    ->withPivot('asignado_por', 'asignado_en');
    }

    /**
     * Relación: Tiene Tribunales (a través de asignaciones_tribunal)
     */
    public function tribunales()
    {
        return $this->belongsToMany(User::class, 'asignaciones_tribunal', 'inscripcion_id', 'tribunal_id')
                    ->wherePivot('activo', true)
                    ->withPivot('asignado_por', 'asignado_en');
    }

    /**
     * Relación: Tiene Documentos (de esta materia)
     */
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'estudiante_id', 'estudiante_id')
                    ->whereHas('tarea', function($q) {
                        $q->where('materia_id', $this->materia_id);
                    });
    }
}