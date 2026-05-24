<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tarea;
use App\Models\Inscripcion;
use App\Models\Notificacion;
use Carbon\Carbon;

class EnviarRecordatoriosVencimiento extends Command
{
    protected $signature = 'recordatorios:vencimiento 
                            {--dias=3 : Número de días antes del vencimiento para enviar recordatorio}
                            {--dry-run : Ejecutar en modo prueba sin guardar notificaciones}';

    protected $description = 'Envía recordatorios a estudiantes N días antes del vencimiento de tareas';

    public function handle()
    {
        $diasAntes = (int) $this->option('dias');
        $dryRun = $this->option('dry-run');
        
        $fechaObjetivo = Carbon::now()->addDays($diasAntes)->startOfDay();
        
        $this->info(" Buscando tareas que vencen el {$fechaObjetivo->format('d/m/Y')}...");

        $tareasProximas = Tarea::whereDate('fecha_limite', $fechaObjetivo)
                              ->with(['materia', 'inscripciones.estudiante'])
                              ->get();

        if ($tareasProximas->isEmpty()) {
            $this->info(' No hay tareas que vencan en esta fecha.');
            return Command::SUCCESS;
        }

        $this->info(" Encontradas {$tareasProximas->count()} tarea(s) próximas a vencer.");

        $recordatoriosEnviados = 0;
        $estudiantesNotificados = 0;

        foreach ($tareasProximas as $tarea) {
            // Extraer nombre de materia para evitar error de sintaxis
            $nombreMateria = $tarea->materia?->nombre ?? 'N/A';
            
            $this->line("\n📄 Tarea: {$tarea->titulo}");
            $this->line("   Materia: {$nombreMateria}");
            $this->line("   Vence: {$tarea->fecha_limite->format('d/m/Y H:i')}");

            foreach ($tarea->inscripciones as $inscripcion) {
                $estudiante = $inscripcion->estudiante;
                
                if (!$estudiante || !$estudiante->activo) {
                    continue;
                }

                // Verificar si el estudiante ya entregó esta tarea
                $yaEntrego = $estudiante->documentos()
                                      ->where('tarea_id', $tarea->id)
                                      ->where('estado_id', '!=', 1)
                                      ->exists();
                
                if ($yaEntrego) {
                    $this->line("   ✓ {$estudiante->nombres} {$estudiante->apellidos} - Ya entregó");
                    continue;
                }

                // Crear notificación de recordatorio
                if (!$dryRun) {
                    Notificacion::crear(
                        usuarioId: $estudiante->id,
                        titulo: '⏰ Recordatorio: Fecha límite próxima',
                        mensaje: "La tarea '{$tarea->titulo}' de {$nombreMateria} vence en {$diasAntes} día(s). ¡Asegúrate de entregar tu documento PDF a tiempo!",
                        tipo: 'recordatorio_limite',
                        entidadRelacionada: "tarea:{$tarea->id}"
                    );
                }

                $this->line("   🔔 {$estudiante->nombres} {$estudiante->apellidos} - Recordatorio " . ($dryRun ? '(PRUEBA)' : 'enviado'));
                $recordatoriosEnviados++;
                $estudiantesNotificados++;
            }
        }

        $this->newLine();
        $this->info(" Resumen:");
        $this->info("   • Tareas procesadas: {$tareasProximas->count()}");
        $this->info("   • Estudiantes notificados: {$estudiantesNotificados}");
        $this->info("   • Recordatorios " . ($dryRun ? 'simulados' : 'enviados') . ": {$recordatoriosEnviados}");

        if ($dryRun) {
            $this->warn("⚠️  Modo PRUEBA: No se guardaron notificaciones en la base de datos.");
            $this->info("💡 Para ejecutar en producción, quita la opción --dry-run");
        }

        return Command::SUCCESS;
    }
}