<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Observacion extends Model
{
    protected $table = 'observaciones';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'documento_id',
        'revisor_id',
        'rol_revisor',
        'comentario',
        'seccion_documento',
        'resuelta',
        'resuelta_en',
        'created_at',
    ];

    protected $casts = [
        'resuelta' => 'boolean',
        'resuelta_en' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id', 'id');
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisor_id', 'id');
    }
}