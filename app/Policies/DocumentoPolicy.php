<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Documento;
use App\Models\AsignacionTutor;
use App\Models\AsignacionTribunal;
use App\Models\Inscripcion;

class DocumentoPolicy
{
    /**
     * Determinar si el usuario puede ver el documento
     */
    public function view(User $user, Documento $documento): bool
    {
        // ESTUDIANTE: Solo puede ver SUS PROPIOS documentos
        if ($user->esEstudiante()) {
            return $user->id === $documento->estudiante_id;
        }

        // TUTOR: Solo puede ver documentos de SUS TUTORADOS
        if ($user->esTutor()) {
            return AsignacionTutor::where('tutor_id', $user->id)
                ->where('activo', true)
                ->whereHas('inscripcion.estudiante', function($q) use ($documento) {
                    $q->where('id', $documento->estudiante_id);
                })->exists();
        }

        // TRIBUNAL: Solo puede ver documentos con estado 'visto_bueno' o superior
        // de SUS ESTUDIANTES ASIGNADOS
        if ($user->esTribunal()) {
            if (!in_array($documento->estado_id, [3, 4, 5])) { // con_observaciones, visto_bueno, aprobado_tribunal
                return false;
            }
            
            return AsignacionTribunal::where('tribunal_id', $user->id)
                ->where('activo', true)
                ->whereHas('inscripcion.estudiante', function($q) use ($documento) {
                    $q->where('id', $documento->estudiante_id);
                })->exists();
        }

        // DOCENTE A CARGO: Puede ver todos los documentos de SU MATERIA
        if ($user->esDocenteCargo()) {
            return Inscripcion::where('docente_cargo_id', $user->id)
                ->whereHas('estudiante.documentos', function($q) use ($documento) {
                    $q->where('id', $documento->id);
                })->exists();
        }

        // DIRECTOR: Puede ver todos los documentos
        if ($user->esDirector()) {
            return true;
        }

        return false;
    }

    /**
     * Determinar si el usuario puede crear documentos
     */
    public function create(User $user): bool
    {
        // Solo ESTUDIANTES pueden crear documentos (subir PDFs)
        return $user->esEstudiante();
    }

    /**
     * Determinar si el usuario puede actualizar el documento
     */
    public function update(User $user, Documento $documento): bool
    {
        // ESTUDIANTE: Solo si es SU documento y está en estado 'entregado' o 'con_observaciones'
        if ($user->esEstudiante()) {
            return $user->id === $documento->estudiante_id 
                && in_array($documento->estado_id, [2, 3]); // entregado, con_observaciones
        }

        // TUTOR: Puede cambiar estado (aprobar/observar) de SUS tutorados
        if ($user->esTutor()) {
            return AsignacionTutor::where('tutor_id', $user->id)
                ->where('activo', true)
                ->whereHas('inscripcion.estudiante', function($q) use ($documento) {
                    $q->where('id', $documento->estudiante_id);
                })->exists();
        }

        // TRIBUNAL: Solo si está en estado 'visto_bueno'
        if ($user->esTribunal()) {
            if ($documento->estado_id !== 4) { // solo visto_bueno
                return false;
            }
            
            return AsignacionTribunal::where('tribunal_id', $user->id)
                ->where('activo', true)
                ->whereHas('inscripcion.estudiante', function($q) use ($documento) {
                    $q->where('id', $documento->estudiante_id);
                })->exists();
        }

        return false;
    }

    /**
     * Determinar si el usuario puede eliminar el documento
     */
    public function delete(User $user, Documento $documento): bool
    {
        // NADIE puede eliminar documentos (para mantener auditoría)
        // Solo se podría permitir al estudiante si está en estado 'entregado' y fue el último en subir
        if ($user->esEstudiante()) {
            return $user->id === $documento->estudiante_id 
                && $documento->estado_id === 2 // entregado
                && $documento->version === 1; // solo primera versión
        }

        return false;
    }

    /**
     * Determinar si el usuario puede aprobar el documento
     */
    public function aprobar(User $user, Documento $documento): bool
    {
        // TUTOR: Solo si es su tutorado y está en estado 'entregado' o 'con_observaciones'
        if ($user->esTutor()) {
            if (!in_array($documento->estado_id, [2, 3])) {
                return false;
            }
            
            return AsignacionTutor::where('tutor_id', $user->id)
                ->where('activo', true)
                ->whereHas('inscripcion.estudiante', function($q) use ($documento) {
                    $q->where('id', $documento->estudiante_id);
                })->exists();
        }

        // TRIBUNAL: Solo si está en estado 'visto_bueno'
        if ($user->esTribunal()) {
            if ($documento->estado_id !== 4) {
                return false;
            }
            
            return AsignacionTribunal::where('tribunal_id', $user->id)
                ->where('activo', true)
                ->whereHas('inscripcion.estudiante', function($q) use ($documento) {
                    $q->where('id', $documento->estudiante_id);
                })->exists();
        }

        return false;
    }

    /**
     * Determinar si el usuario puede agregar observaciones
     */
    public function observar(User $user, Documento $documento): bool
    {
        // TUTOR o TRIBUNAL pueden observar
        if ($user->esTutor() || $user->esTribunal()) {
            // El documento no puede estar aprobado definitivamente
            if ($documento->estado_id === 5) { // aprobado_tribunal
                return false;
            }
            
            // Verificar que el documento pertenece a sus asignaciones
            if ($user->esTutor()) {
                return AsignacionTutor::where('tutor_id', $user->id)
                    ->where('activo', true)
                    ->whereHas('inscripcion.estudiante', function($q) use ($documento) {
                        $q->where('id', $documento->estudiante_id);
                    })->exists();
            }
            
            if ($user->esTribunal()) {
                return AsignacionTribunal::where('tribunal_id', $user->id)
                    ->where('activo', true)
                    ->whereHas('inscripcion.estudiante', function($q) use ($documento) {
                        $q->where('id', $documento->estudiante_id);
                    })->exists();
            }
        }

        return false;
    }
}