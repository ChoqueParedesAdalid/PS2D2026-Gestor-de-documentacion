<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    
    protected $fillable = [
        'usuario_id',
        'titulo',
        'mensaje',
        'tipo',
        'leida',
        'entidad_relacionada'
    ];
    
    protected $casts = [
        'leida' => 'boolean',
    ];
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}