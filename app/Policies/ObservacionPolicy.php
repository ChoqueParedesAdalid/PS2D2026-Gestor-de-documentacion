<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Observacion;

class ObservacionPolicy
{
    public function view(User $user, Observacion $observacion): bool
    {
        // ESTUDIANTE: Solo observaciones en SUS documentos
        if ($user->esEstudiante()) {
            return $observacion->documento->estudiante_id === $user->id;
        }

        // TUTOR o TRIBUNAL: Solo observaciones de SUS asignaciones
        if ($user->esTutor() || $user->esTribunal()) {
            return $observacion->revisor_id === $user->id 
                || $observacion->documento->estudiante_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Solo TUTOR y TRIBUNAL pueden crear observaciones
        return $user->esTutor() || $user->esTribunal();
    }

    public function update(User $user, Observacion $observacion): bool
    {
        // Solo el creador puede editar su propia observación
        return $observacion->revisor_id === $user->id;
    }

    public function delete(User $user, Observacion $observacion): bool
    {
        // Solo el creador puede eliminar su propia observación
        return $observacion->revisor_id === $user->id;
    }

    public function marcarCorregida(User $user, Observacion $observacion): bool
    {
        // Solo el ESTUDIANTE dueño del documento puede marcar como corregida
        if ($user->esEstudiante()) {
            return $observacion->documento->estudiante_id === $user->id;
        }

        // O el mismo revisor puede marcar
        return $observacion->revisor_id === $user->id;
    }
}