<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Configuración de la tabla
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    // Desactivar timestamps automáticos (usamos created_at manual)
    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombres',
        'apellidos',
        'email_institucional',
        'password_hash',
        'role_id',
        'activo',
    ];

    // Campos ocultos (no se retornan en JSON)
    protected $hidden = [
        'password_hash',
    ];

    // Casts de atributos
    protected $casts = [
        'activo' => 'boolean',
        'role_id' => 'integer',
        'created_at' => 'datetime',
    ];

    // ==================================================
    // RELACIONES CON OTRAS TABLAS
    // ==================================================

    /**
     * Relación: Pertenece a un Rol
     */
    public function rol()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Relación: Tiene una inscripción (como estudiante)
     */
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'estudiante_id', 'id');
    }

    /**
     * Relación: Tiene documentos entregados (como estudiante)
     */
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'estudiante_id', 'id');
    }

    /**
     * Relación: Es docente a cargo de inscripciones
     */
    public function docenteCargoInscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'docente_cargo_id', 'id');
    }

    /**
     * Relación: Es tutor en asignaciones
     */
    public function asignacionesTutor()
    {
        return $this->hasMany(AsignacionTutor::class, 'tutor_id', 'id');
    }

    /**
     * Relación: Es tribunal en asignaciones
     */
    public function asignacionesTribunal()
    {
        return $this->hasMany(AsignacionTribunal::class, 'tribunal_id', 'id');
    }

    /**
     * Relación: Creó tareas
     */
    public function tareasCreadas()
    {
        return $this->hasMany(Tarea::class, 'creada_por', 'id');
    }

    /**
     * Relación: Creó observaciones
     */
    public function observacionesCreadas()
    {
        return $this->hasMany(Observacion::class, 'revisor_id', 'id');
    }

    /**
     * Relación: Tiene notificaciones
     */
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id', 'id')
                    ->orderBy('fecha_creacion', 'desc');
    }

    // ==================================================
    // MÉTODOS DE UTILIDAD PARA ROLES
    // ==================================================

    /**
     * Verificar si es estudiante
     */
    public function esEstudiante()
    {
        return $this->rol?->nombre === 'estudiante';
    }

    /**
     * Verificar si es tutor
     */
    public function esTutor()
    {
        return $this->rol?->nombre === 'tutor';
    }

    /**
     * Verificar si es tribunal
     */
    public function esTribunal()
    {
        return $this->rol?->nombre === 'tribunal';
    }

    /**
     * Verificar si es docente a cargo
     */
    public function esDocenteCargo()
    {
        return $this->rol?->nombre === 'docente_cargo';
    }

    /**
     * Verificar si es director
     */
    public function esDirector()
    {
        return $this->rol?->nombre === 'director';
    }

    /**
     * Verificar si es docente (genérico o específico)
     */
    public function esDocente()
    {
        return in_array($this->rol?->nombre, ['docente', 'docente_cargo', 'tutor', 'tribunal']);
    }

    // ==================================================
    // MÉTODOS DE ACCESO (ATTRIBUTES)
    // ==================================================

    /**
     * Obtener nombre completo
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    /**
     * Obtener avatar inicial
     */
    public function getAvatarInicialAttribute()
    {
        return strtoupper(substr($this->nombres, 0, 1));
    }

    // ==================================================
    // MÉTODOS DE AUTENTICACIÓN (OVERRIDES)
    // ==================================================

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Get the e-mail address where password reset links are sent.
     */
    public function getEmailForPasswordReset()
    {
        return $this->email_institucional;
    }
}