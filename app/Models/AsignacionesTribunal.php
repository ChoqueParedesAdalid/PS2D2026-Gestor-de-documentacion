<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionesTribunal extends Model
{
    protected $table = 'asignaciones_tribunal';
    
    protected $fillable = [
        'inscripcion_id',
        'tribunal_id',
        'tipo',
        'activo',
        'fecha_asignacion'
    ];
    
    protected $casts = [
        'activo' => 'boolean',
        'fecha_asignacion' => 'date',
    ];
    
    public function tribunal()
    {
        return $this->belongsTo(User::class, 'tribunal_id');
    }
    
    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }
}