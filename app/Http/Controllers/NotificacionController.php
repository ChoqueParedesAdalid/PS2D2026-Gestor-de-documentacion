<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notificacion;

class NotificacionController extends Controller
{
    /**
     * Obtener notificaciones del usuario actual (API)
     */
    public function obtenerNotificaciones()
    {
        try {
            $user = Auth::user();
            
            // Obtener las últimas 10 notificaciones
            $notificaciones = Notificacion::where('usuario_id', $user->id)
                                         ->orderBy('created_at', 'desc')
                                         ->limit(10)
                                         ->get()
                                         ->map(function($notif) {
                                             return [
                                                 'id' => $notif->id,
                                                 'titulo' => $notif->titulo,
                                                 'mensaje' => $notif->mensaje,
                                                 'tipo' => $notif->tipo,
                                                 'leida' => (bool) $notif->leida,
                                                 'entidad_relacionada' => $notif->entidad_relacionada,
                                                 'fecha_creacion' => $notif->created_at->diffForHumans(),
                                                 'icono' => $this->getIconoPorTipo($notif->tipo),
                                             ];
                                         });
            
            // Contar no leídas
            $noLeidas = Notificacion::where('usuario_id', $user->id)
                                   ->where('leida', false)
                                   ->count();
            
            return response()->json([
                'success' => true,
                'notificaciones' => $notificaciones,
                'no_leidas' => $noLeidas,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar notificaciones: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Marcar notificación como leída
     */
    public function marcarComoLeida($id)
    {
        try {
            $notif = Notificacion::where('id', $id)
                                ->where('usuario_id', Auth::id())
                                ->firstOrFail();
            
            $notif->leida = true;
            $notif->save();
            
            $noLeidas = Notificacion::where('usuario_id', Auth::id())
                                   ->where('leida', false)
                                   ->count();
            
            return response()->json([
                'success' => true,
                'no_leidas' => $noLeidas,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar como leída',
            ], 500);
        }
    }
    
    /**
     * Marcar todas como leídas
     */
    public function marcarTodasLeidas()
    {
        try {
            Notificacion::where('usuario_id', Auth::id())
                       ->where('leida', false)
                       ->update(['leida' => true]);
            
            return response()->json([
                'success' => true,
                'no_leidas' => 0,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar todas como leídas',
            ], 500);
        }
    }
    
    /**
     * Página de todas las notificaciones
     */
    public function index()
    {
        $notificaciones = Notificacion::where('usuario_id', Auth::id())
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(20);
        
        return view('notificaciones.index', compact('notificaciones'));
    }
    
    /**
     * Obtener icono según tipo de notificación
     */
    private function getIconoPorTipo($tipo)
    {
        $iconos = [
            'nueva_entrega' => 'fas fa-file-upload text-blue-400',
            'nueva_observacion' => 'fas fa-comment-alt text-yellow-400',
            'documento_aprobado' => 'fas fa-check-circle text-green-400',
            'aprobado_final' => 'fas fa-trophy text-yellow-400',
            'aprobado_tribunal' => 'fas fa-gavel text-purple-400',
            'recordatorio_limite' => 'fas fa-clock text-orange-400',
            'nueva_inscripcion' => 'fas fa-user-plus text-blue-400',
            'asignacion_tutor' => 'fas fa-chalkboard-teacher text-blue-400',
            'asignacion_tribunal' => 'fas fa-gavel text-purple-400',
            'nueva_tarea' => 'fas fa-tasks text-green-400',
        ];
        
        return $iconos[$tipo] ?? 'fas fa-bell text-gray-400';
    }
}