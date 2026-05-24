<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionTribunal extends Model
{
    protected $table = 'asignaciones_tribunal';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'inscripcion_id',
        'tribunal_id',
        'asignado_por',
        'asignado_en',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'asignado_en' => 'datetime',
    ];

    public function tribunal()
    {
        return $this->belongsTo(User::class, 'tribunal_id', 'id');
    }

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'inscripcion_id', 'id');
    }
}