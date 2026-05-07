<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    protected $table = 'tareas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'materia_id',
        'titulo',
        'descripcion',
        'fecha_limite',
        'tipo_documento',
        'creada_por',
    ];

    protected $casts = [
        'fecha_limite' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id', 'id');
    }

    public function creadaPor()
    {
        return $this->belongsTo(User::class, 'creada_por', 'id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'tarea_id', 'id');
    }
}