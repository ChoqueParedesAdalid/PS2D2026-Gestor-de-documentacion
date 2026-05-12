<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Configuración de tabla y llave primaria
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'email_institucional',
        'nombres',
        'apellidos',
        'password_hash',
        'role_id',
        'ci',
        'telefono',
        'activo',
    ];

    // Campos ocultos (no se retornan en JSON)
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    // Mapeo del campo de contraseña para Laravel Auth
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Campos que deben ser casteados
    protected $casts = [
        'activo' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    // ==================================================
    // RELACIONES CON OTRAS TABLAS
    // ==================================================

    // Relación con rol
    public function rol()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    // Relación: Si es estudiante → tiene inscripciones
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'estudiante_id', 'id');
    }

    // Relación: Si es tutor → tiene estudiantes asignados
    public function tutorados()
    {
        return $this->belongsToMany(
            User::class,
            'asignaciones_tutor',
            'tutor_id',
            'inscripcion_id'
        )->withPivot('id as asignacion_id', 'asignado_en', 'activo')
         ->wherePivot('activo', true);
    }

    // Relación: Si es docente → puede ser tutor o tribunal
    public function asignacionesComoTutor()
    {
        return $this->hasMany(AsignacionTutor::class, 'tutor_id', 'id');
    }

    public function asignacionesComoTribunal()
    {
        return $this->hasMany(AsignacionTribunal::class, 'tribunal_id', 'id');
    }

    // Relación: Observaciones que ha hecho este usuario
    public function observacionesRealizadas()
    {
        return $this->hasMany(Observacion::class, 'revisor_id', 'id');
    }

    // Relación: Notificaciones recibidas
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id', 'id')
                    ->orderBy('created_at', 'desc');
    }

    // ==================================================
    // SCOPES PARA CONSULTAS FRECUENTES
    // ==================================================

    // Scope: Solo usuarios activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Scope: Filtrar por rol (nombre del rol)
    public function scopePorRol($query, $nombreRol)
    {
        return $query->whereHas('rol', function($q) use ($nombreRol) {
            $q->where('nombre', $nombreRol);
        });
    }

    // Scope: Validar correo institucional Univalle
    public function scopeEsCorreoUnivalle($query)
    {
        return $query->where(function($q) {
            $q->where('email_institucional', 'LIKE', '%@univalle.edu')
              ->orWhere('email_institucional', 'LIKE', '%@est.univalle.edu');
        });
    }

    // ==================================================
    // MÉTODOS DE UTILIDAD
    // ==================================================

    // Obtener nombre completo
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    // Verificar si es estudiante
    public function esEstudiante()
    {
        return $this->rol?->nombre === 'estudiante';
    }

    // Verificar si es tutor
    public function esTutor()
    {
        return $this->rol?->nombre === 'tutor';
    }

    // Verificar si es docente a cargo
    public function esDocenteCargo()
    {
        return $this->rol?->nombre === 'docente_cargo';
    }

    // Verificar si es tribunal
    public function esTribunal()
    {
        return $this->rol?->nombre === 'tribunal';
    }

    // Verificar si es director
    public function esDirector()
    {
        return $this->rol?->nombre === 'director';
    }
}