<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Inscripcion;

class InscripcionPolicy
{
    public function view(User $user, Inscripcion $inscripcion): bool
    {
        // ESTUDIANTE: Solo su propia inscripción
        if ($user->esEstudiante()) {
            return $inscripcion->estudiante_id === $user->id;
        }

        // TUTOR: Solo inscripciones de SUS tutorados
        if ($user->esTutor()) {
            return $inscripcion->tutores()->where('tutor_id', $user->id)->exists();
        }

        // DOCENTE A CARGO: Solo inscripciones de SU materia
        if ($user->esDocenteCargo()) {
            return $inscripcion->docente_cargo_id === $user->id;
        }

        // DIRECTOR: Todas
        if ($user->esDirector()) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Solo DOCENTE A CARGO o DIRECTOR pueden crear inscripciones
        return $user->esDocenteCargo() || $user->esDirector();
    }

    public function update(User $user, Inscripcion $inscripcion): bool
    {
        // Solo DOCENTE A CARGO puede actualizar
        return $user->esDocenteCargo() && $inscripcion->docente_cargo_id === $user->id;
    }
}