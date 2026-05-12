<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionesTutor extends Model
{
    protected $table = 'asignaciones_tutor';
    
    protected $fillable = [
        'inscripcion_id',
        'tutor_id',
        'activo',
        'fecha_asignacion'
    ];
    
    protected $casts = [
        'activo' => 'boolean',
        'fecha_asignacion' => 'date',
    ];
    
    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }
    
    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }
}