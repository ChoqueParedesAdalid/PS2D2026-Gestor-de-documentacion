<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionTutor extends Model
{
    protected $table = 'asignaciones_tutor';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'inscripcion_id',
        'tutor_id',
        'asignado_por',
        'asignado_en',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'asignado_en' => 'datetime',
    ];

    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id', 'id');
    }

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'inscripcion_id', 'id');
    }
}