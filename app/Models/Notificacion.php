<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'id';
    public $timestamps = false; // Usamos created_at manual

    protected $fillable = [
        'usuario_id',
        'titulo',
        'mensaje',
        'tipo',
        'leida',
        'entidad_relacionada',
        'created_at',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }
    
    // Método estático para crear notificación (si ya lo tienes, ignóralo)
    public static function crear($usuarioId, $titulo, $mensaje, $tipo, $entidadRelacionada = null)
    {
        return static::create([
            'usuario_id' => $usuarioId,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'leida' => false,
            'entidad_relacionada' => $entidadRelacionada,
            'created_at' => now(),
        ]);
    }
}