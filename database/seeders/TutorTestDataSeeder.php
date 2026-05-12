<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Gestion;
use App\Models\Materia;
use App\Models\Inscripcion;
use App\Models\AsignacionTutor;
use App\Models\Tarea;
use App\Models\EstadoDocumento;
use App\Models\Documento;
use Illuminate\Support\Facades\Hash;

class TutorTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🌱 Iniciando seed de datos de prueba para Tutor...\n\n";

        // ==================================================
        // 1. OBTENER ROLES
        // ==================================================
        $rolTutor = Role::where('nombre', 'tutor')->first();
        $rolEstudiante = Role::where('nombre', 'estudiante')->first();
        
        if (!$rolTutor || !$rolEstudiante) {
            echo "❌ Error: No se encontraron los roles 'tutor' o 'estudiante'\n";
            return;
        }
        
        echo "✅ Roles encontrados\n";

        // ==================================================
        // 2. OBTENER TUTOR ACTUAL (el que iniciaste sesión)
        // ==================================================
        // Como no podemos usar auth() en seeders, buscamos por email
        $tutor = User::where('email_institucional', 'kmansillaa@univalle.edu')->first();
        
        if (!$tutor) {
            echo "⚠️  Creando tutor de prueba...\n";
            $tutor = User::create([
                'email_institucional' => 'kmansillaa@univalle.edu',
                'nombres' => 'Karina',
                'apellidos' => 'Mansilla',
                'password_hash' => bcrypt('password123'),
                'role_id' => $rolTutor->id,
                'activo' => true,
            ]);
        }
        
        echo "✅ Tutor: {$tutor->email_institucional}\n";

        // ==================================================
        // 3. CREAR ESTUDIANTE DE PRUEBA
        // ==================================================
        $estudiante = User::firstOrCreate(
            ['email_institucional' => 'sqj2029983@est.univalle.edu'],
            [
                'nombres' => 'Jeysi',
                'apellidos' => 'Siñani',
                'password_hash' => bcrypt('password123'),
                'role_id' => $rolEstudiante->id,
                'activo' => true,
            ]
        );
        
        echo "✅ Estudiante: {$estudiante->nombres} {$estudiante->apellidos}\n";

        // ==================================================
        // 4. CREAR GESTIÓN Y MATERIA
        // ==================================================
        $gestion = Gestion::firstOrCreate(
            ['nombre' => '2026-1'],
            [
                'fecha_inicio' => '2026-03-01',
                'fecha_fin' => '2026-07-31',
                'activa' => true,
            ]
        );

        $materia = Materia::firstOrCreate(
            ['nombre' => 'Proyecto de Sistemas 2'],
            [
                'descripcion' => 'Documento final y defensa',
                'semestre_requerido' => '8vo',
                'gestion_id' => $gestion->id,
            ]
        );
        
        echo "✅ Materia: {$materia->nombre}\n";

        // ==================================================
        // 5. CREAR INSCRIPCIÓN
        // ==================================================
        $inscripcion = Inscripcion::firstOrCreate(
            [
                'estudiante_id' => $estudiante->id,
                'materia_id' => $materia->id,
            ],
            [
                'docente_cargo_id' => 1, // Ajusta si tienes un docente_cargo real
                'titulo_proyecto' => 'Sistema de Gestión Documental - DocGest',
                'estado_inscripcion' => 'activo',
            ]
        );
        
        echo "✅ Inscripción creada (ID: {$inscripcion->id})\n";

        // ==================================================
        // 6. CREAR ASIGNACIÓN TUTOR-ESTUDIANTE (¡CLAVE!)
        // ==================================================
        $asignacion = AsignacionTutor::firstOrCreate(
            [
                'inscripcion_id' => $inscripcion->id,
                'tutor_id' => $tutor->id,
            ],
            [
                'asignado_por' => 1,
                'asignado_en' => now(),
                'activo' => true,
            ]
        );
        
        echo "✅ Asignación tutor-estudiante creada\n";

        // ==================================================
        // 7. CREAR TAREA
        // ==================================================
        $tarea = Tarea::firstOrCreate(
            [
                'materia_id' => $materia->id,
                'titulo' => 'Entrega Capítulo 1 - Introducción',
            ],
            [
                'descripcion' => 'Subir introducción, objetivos y justificación del proyecto',
                'fecha_limite' => now()->addDays(7),
                'tipo_documento' => 'documento_final',
                'creada_por' => 1,
            ]
        );
        
        echo "✅ Tarea creada: {$tarea->titulo}\n";

        // ==================================================
        // 8. OBTENER ESTADOS DE DOCUMENTO
        // ==================================================
        $estadoEntregado = EstadoDocumento::where('nombre', 'entregado')->first();
        $estadoAprobado = EstadoDocumento::where('nombre', 'visto_bueno')->first();
        $estadoPendiente = EstadoDocumento::where('nombre', 'no_entregado')->first();
        
        if (!$estadoEntregado) {
            echo "❌ Error: No se encontró el estado 'entregado'\n";
            return;
        }

        // ==================================================
        // 9. CREAR DOCUMENTOS DE PRUEBA
        // ==================================================
        
        // Documento 1: Pendiente de revisión (entregado)
        $documento1 = Documento::create([
            'tarea_id' => $tarea->id,
            'estudiante_id' => $estudiante->id,
            'version' => 1,
            'archivo_ruta' => 'uploads/documentos/2026-1/capitulo1_v1.pdf',
            'archivo_nombre_original' => 'Capitulo1_Introduccion.pdf',
            'archivo_tamaño' => 245678,
            'archivo_hash' => hash('sha256', 'contenido_ejemplo_1'),
            'estado_id' => $estadoEntregado->id,
            'entregado_en' => now()->subDays(2),
        ]);
        
        echo "✅ Documento 1 creado: {$documento1->archivo_nombre_original} (Entregado)\n";

        // Documento 2: Aprobado (visto_bueno) - si existe el estado
        if ($estadoAprobado) {
            $documento2 = Documento::create([
                'tarea_id' => $tarea->id,
                'estudiante_id' => $estudiante->id,
                'version' => 2,
                'archivo_ruta' => 'uploads/documentos/2026-1/capitulo1_v2.pdf',
                'archivo_nombre_original' => 'Capitulo1_Introduccion_Corregido.pdf',
                'archivo_tamaño' => 267890,
                'archivo_hash' => hash('sha256', 'contenido_ejemplo_2'),
                'estado_id' => $estadoAprobado->id,
                'entregado_en' => now()->subDays(5),
            ]);
            
            echo "✅ Documento 2 creado: {$documento2->archivo_nombre_original} (Aprobado)\n";
        }

        // ==================================================
        // 10. RESUMEN FINAL
        // ==================================================
        echo "\n";
        echo "╔════════════════════════════════════════════╗\n";
        echo "║   🎉 DATOS DE PRUEBA CREADOS EXITOSAMENTE  ║\n";
        echo "╚════════════════════════════════════════════╝\n";
        echo "\n";
        echo "📊 Resumen:\n";
        echo "  • Tutor: {$tutor->nombres} {$tutor->apellidos}\n";
        echo "  • Estudiante: {$estudiante->nombres} {$estudiante->apellidos}\n";
        echo "  • Inscripciones: 1\n";
        echo "  • Asignaciones tutor: 1\n";
        echo "  • Tareas: 1\n";
        echo "  • Documentos: " . Documento::where('estudiante_id', $estudiante->id)->count() . "\n";
        echo "\n";
        echo "🔐 Credenciales de prueba:\n";
        echo "  • Tutor: kmansillaa@univalle.edu / password123\n";
        echo "  • Estudiante: sqj2029983@est.univalle.edu / password123\n";
        echo "\n";
        echo "🌐 Ahora visita: http://127.0.0.1:8000/tutor\n";
        echo "\n";
    }
}