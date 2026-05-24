<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    // Configuración de tabla y llave primaria
    protected $table = 'documentos';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    // Desactivar timestamps automáticos de Laravel (usamos created_at manual)
    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'tarea_id',
        'estudiante_id',
        'version',
        'archivo_ruta',
        'archivo_nombre_original',
        'archivo_tamaño',
        'archivo_hash',
        'estado_id',
        'entregado_en',
        'created_at',
    ];

    // Campos que deben ser casteados a tipos específicos
    protected $casts = [
        'version' => 'integer',
        'archivo_tamaño' => 'integer',
        'estado_id' => 'integer',
        'entregado_en' => 'datetime',
        'created_at' => 'datetime',
    ];

    // ==================================================
    // RELACIONES CON OTRAS TABLAS
    // ==================================================

    /**
     * Relación: Pertenece a una Tarea
     */
    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'tarea_id', 'id');
    }

    /**
     * Relación: Pertenece a un Estudiante (Usuario)
     */
    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id', 'id');
    }

    /**
     * Relación: Tiene un Estado de documento
     */
    public function estado()
    {
        return $this->belongsTo(EstadoDocumento::class, 'estado_id', 'id');
    }

    /**
     * Relación: Tiene muchas Observaciones
     */
    public function observaciones()
    {
        return $this->hasMany(Observacion::class, 'documento_id', 'id')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Relación: Tiene historial de versiones
     */
    //public function historial()
    //{
        //return $this->hasMany(DocumentoHistorial::class, 'documento_id', 'id')
                   // ->orderBy('version', 'desc');
    //}

    /**
     * Relación: Pertenece a un Proyecto (a través de la tarea o estudiante)
     */
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id', 'id');
    }

    // ==================================================
    // SCOPES PARA CONSULTAS FRECUENTES
    // ==================================================

    /**
     * Scope: Filtrar documentos por estado (nombre del estado)
     */
    public function scopePorEstado($query, $nombreEstado)
    {
        return $query->whereHas('estado', function($q) use ($nombreEstado) {
            $q->where('nombre', $nombreEstado);
        });
    }

    /**
     * Scope: Filtrar documentos entregados (no null)
     */
    public function scopeEntregados($query)
    {
        return $query->whereNotNull('entregado_en');
    }

    /**
     * Scope: Filtrar documentos pendientes de revisión
     */
    public function scopePendientes($query)
    {
        return $query->whereHas('estado', function($q) {
            $q->where('nombre', 'entregado');
        });
    }

    /**
     * Scope: Filtrar documentos con observaciones sin resolver
     */
    public function scopeConObservacionesPendientes($query)
    {
        return $query->whereHas('observaciones', function($q) {
            $q->where('resuelta', false);
        });
    }

    /**
     * Scope: Ordenar por fecha de entrega (más recientes primero)
     */
    public function scopeRecientes($query)
    {
        return $query->orderBy('entregado_en', 'desc');
    }

    /**
     * Scope: Filtrar por estudiante específico
     */
    public function scopeDeEstudiante($query, $estudianteId)
    {
        return $query->where('estudiante_id', $estudianteId);
    }

    /**
     * Scope: Filtrar por tarea específica
     */
    public function scopeDeTarea($query, $tareaId)
    {
        return $query->where('tarea_id', $tareaId);
    }

    // ==================================================
    // MÉTODOS DE UTILIDAD
    // ==================================================

    /**
     * Obtener la extensión del archivo
     */
    public function getExtensionAttribute()
    {
        return pathinfo($this->archivo_nombre_original, PATHINFO_EXTENSION);
    }

    /**
     * Verificar si es un PDF
     */
    public function esPdf()
    {
        return strtolower($this->getExtensionAttribute()) === 'pdf';
    }

    /**
     * Obtener tamaño formateado (KB, MB, GB)
     */
    public function getTamañoFormateadoAttribute()
    {
        $bytes = $this->archivo_tamaño;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' bytes';
    }

    /**
     * Verificar si el documento está aprobado (visto_bueno o aprobado_tribunal)
     */
    public function estaAprobado()
    {
        return in_array($this->estado?->nombre, ['visto_bueno', 'aprobado_tribunal']);
    }

    /**
     * Verificar si el documento tiene observaciones sin resolver
     */
    public function tieneObservacionesPendientes()
    {
        return $this->observaciones()->where('resuelta', false)->exists();
    }

    /**
     * Obtener la última versión de este documento (para la misma tarea y estudiante)
     */
    public static function ultimaVersion($tareaId, $estudianteId)
    {
        return static::where('tarea_id', $tareaId)
                    ->where('estudiante_id', $estudianteId)
                    ->orderBy('version', 'desc')
                    ->first();
    }

    /**
     * Generar el nombre del archivo para la siguiente versión
     */
    public static function generarNombreVersion($nombreOriginal, $versionNueva)
    {
        $pathinfo = pathinfo($nombreOriginal);
        $nombre = $pathinfo['filename'];
        $extension = $pathinfo['extension'] ?? '';
        
        return "{$nombre}_v{$versionNueva}.{$extension}";
    }
}