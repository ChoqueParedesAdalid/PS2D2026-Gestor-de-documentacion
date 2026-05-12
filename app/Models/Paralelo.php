<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paralelo extends Model
{
    protected $table = 'paralelos';
    
    protected $fillable = [
        'gestion_id',
        'materia_id',
        'letra',
        'docente_cargo_id',
        'fecha_inicio',
        'fecha_fin',
        'estado'
    ];
    
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];
    
    public function estudiantes()
{
    return $this->belongsToMany(User::class, 'inscripciones', 'paralelo_id', 'estudiante_id')
                ->withPivot('estado_inscripcion')
                ->withTimestamps();
}
    // ✅ Relación con Gestion (ya corregido antes)
    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }
    
    // ✅ Relación con Materia
    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }
    
    // ✅ CORREGIDO: User en lugar de Usuario
    public function docenteCargo()
    {
        return $this->belongsTo(User::class, 'docente_cargo_id');
    }
    
    // ✅ Relación con Inscripciones
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }
}