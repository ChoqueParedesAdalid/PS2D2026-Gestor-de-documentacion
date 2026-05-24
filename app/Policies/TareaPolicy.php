<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tarea;
use App\Models\Inscripcion;

class TareaPolicy
{
    public function view(User $user, Tarea $tarea): bool
    {
        // ESTUDIANTE: Solo tareas de SU materia inscrita
        if ($user->esEstudiante()) {
            return Inscripcion::where('estudiante_id', $user->id)
                ->where('materia_id', $tarea->materia_id)
                ->where('estado_inscripcion', 'activo')
                ->exists();
        }

        // DOCENTE A CARGO: Todas las tareas de SU materia
        if ($user->esDocenteCargo()) {
            return Inscripcion::where('docente_cargo_id', $user->id)
                ->where('materia_id', $tarea->materia_id)
                ->exists();
        }

        // TUTOR y TRIBUNAL: Pueden ver todas (para contexto)
        if ($user->esTutor() || $user->esTribunal()) {
            return true;
        }

        // DIRECTOR: Todas
        if ($user->esDirector()) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Solo DOCENTE A CARGO puede crear tareas
        return $user->esDocenteCargo();
    }

    public function update(User $user, Tarea $tarea): bool
    {
        // Solo DOCENTE A CARGO puede editar tareas
        return $user->esDocenteCargo() 
            && Inscripcion::where('docente_cargo_id', $user->id)
                ->where('materia_id', $tarea->materia_id)
                ->exists();
    }

    public function delete(User $user, Tarea $tarea): bool
    {
        // Solo DOCENTE A CARGO puede eliminar (y solo si no tiene entregas)
        if (!$user->esDocenteCargo()) {
            return false;
        }

        // Verificar que no haya documentos entregados
        return $tarea->documentos()->count() === 0;
    }
}