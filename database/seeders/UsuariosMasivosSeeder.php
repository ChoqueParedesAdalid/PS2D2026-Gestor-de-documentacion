<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UsuariosMasivosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════╗\n";
        echo "║          🌱 CREANDO USUARIOS MASIVOS                ║\n";
        echo "╚══════════════════════════════════════════════════════╝\n";
        echo "\n";

        // Obtener roles
        $rolDirector = Role::where('nombre', 'director')->first();
        $rolDocenteCargo = Role::where('nombre', 'docente_cargo')->first();
        $rolDocente = Role::where('nombre', 'docente')->first();
        $rolEstudiante = Role::where('nombre', 'estudiante')->first();

        if (!$rolDirector || !$rolDocenteCargo || !$rolDocente || !$rolEstudiante) {
            echo "❌ Error: No se encontraron todos los roles necesarios.\n";
            echo "   Asegúrate de haber ejecutado: ALTER TABLE para agregar 'docente' al ENUM\n";
            echo "   Y de haber insertado el rol 'docente' en la tabla roles.\n";
            return;
        }

        // Contraseña común para todos (SE ENCRIPTA CON BCRYPT - FUNCIONA PERFECTO)
        $password = bcrypt('password123');

        // ==================================================
        // 1. CREAR DIRECTOR
        // ==================================================
        echo "👑 Creando Director...\n";
        
        User::firstOrCreate(
            ['email_institucional' => 'LSanchez@univalle.edu'],
            [
                'nombres' => 'Limbert',
                'apellidos' => 'Sanchez Mendoza',
                'password_hash' => $password,
                'role_id' => $rolDirector->id,
                'activo' => true,
            ]
        );
        echo "   ✅ Director: LSanchez@univalle.edu / password123\n\n";

        // ==================================================
        // 2. CREAR DOCENTE A CARGO (1 docente)
        // ==================================================
        echo "👨‍🏫 Creando Docente a Cargo...\n";
        
        User::firstOrCreate(
            ['email_institucional' => 'docente.cargo@univalle.edu'],
            [
                'nombres' => 'Ana',
                'apellidos' => 'Profesora',
                'password_hash' => $password,
                'role_id' => $rolDocenteCargo->id,
                'activo' => true,
            ]
        );
        echo "   ✅ Docente a Cargo: docente.cargo@univalle.edu / password123\n\n";

        // ==================================================
        // 3. CREAR 20 DOCENTES (Rol genérico: 'docente')
        // ==================================================
        echo "👨‍💼 Creando 20 Docentes (Rol Genérico)...\n";
        echo "   ⚠️  Estos docentes tendrán rol 'docente' hasta que el Docente a Cargo\n";
        echo "      los asigne como Tutor o Tribunal de estudiantes específicos.\n\n";
        
        $nombresDocentes = [
            'Carlos', 'Ana', 'Luis', 'María', 'Jorge',
            'Patricia', 'Roberto', 'Laura', 'Miguel', 'Sandra',
            'Fernando', 'Carmen', 'Diego', 'Rosa', 'Pablo',
            'Elena', 'Ricardo', 'Mónica', 'Andrés', 'Gabriela'
        ];

        $apellidosDocentes = [
            'Mamani', 'Quispe', 'Flores', 'Condori', 'Huanca',
            'Apaza', 'Mamani', 'Choque', 'Calle', 'Nina',
            'Quispe', 'Mamani', 'Cruz', 'López', 'García',
            'Martínez', 'Rodríguez', 'Fernández', 'González', 'Pérez'
        ];

        for ($i = 0; $i < 20; $i++) {
            $email = "docente" . ($i + 1) . "@univalle.edu";
            
            User::firstOrCreate(
                ['email_institucional' => $email],
                [
                    'nombres' => $nombresDocentes[$i],
                    'apellidos' => $apellidosDocentes[$i],
                    'password_hash' => $password,
                    'role_id' => $rolDocente->id,
                    'activo' => true,
                ]
            );
            
            echo "   ✅ Docente " . ($i + 1) . ": {$email} / password123\n";
        }
        echo "\n";

        // ==================================================
        // 4. CREAR 30 ESTUDIANTES
        // ==================================================
        echo "🎓 Creando 30 Estudiantes...\n";
        
        for ($i = 1; $i <= 30; $i++) {
            $email = "est" . str_pad($i, 2, '0', STR_PAD_LEFT) . "@est.univalle.edu";
            
            User::firstOrCreate(
                ['email_institucional' => $email],
                [
                    'nombres' => 'Estudiante',
                    'apellidos' => 'Prueba ' . $i,
                    'password_hash' => $password,
                    'role_id' => $rolEstudiante->id,
                    'activo' => true,
                ]
            );
            
            echo "   ✅ Estudiante " . $i . ": {$email} / password123\n";
        }
        echo "\n";

        echo "╔══════════════════════════════════════════════════════╗\n";
        echo "║            ✅ USUARIOS CREADOS EXITOSAMENTE          ║\n";
        echo "╚══════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "📊 RESUMEN:\n";
        echo "   • Director: 1 (LSanchez@univalle.edu)\n";
        echo "   • Docente a Cargo: 1 (docente.cargo@univalle.edu)\n";
        echo "   • Docentes (rol genérico): 20\n";
        echo "   • Estudiantes: 30\n";
        echo "\n";
        echo "🔐 CONTRASEÑA PARA TODOS: password123\n";
        echo "\n";
        echo "💡 NOTA:\n";
        echo "   Los 20 docentes tienen rol 'docente' (genérico).\n";
        echo "   El Docente a Cargo deberá asignarlos como Tutores o\n";
        echo "   Tribunales de estudiantes específicos.\n";
        echo "\n";
    }
}