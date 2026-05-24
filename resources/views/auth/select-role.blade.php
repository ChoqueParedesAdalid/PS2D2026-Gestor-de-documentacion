<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Rol - DocGest</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-red-950 to-black min-h-screen flex items-center justify-center p-4">

    <div class="bg-black bg-opacity-80 backdrop-blur-lg rounded-2xl shadow-2xl p-8 max-w-md w-full border border-gray-800">
        
        <!-- Logo / Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-700 rounded-full mb-4">
                <i class="fas fa-user-shield text-2xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">¡Hola, {{ $user->nombres }}!</h1>
            <p class="text-gray-400 mt-2">Selecciona el rol con el que deseas ingresar hoy.</p>
        </div>

        <!-- Opciones de Rol -->
        <form action="{{ route('auth.role.process') }}" method="POST" class="space-y-4">
            @csrf

            @foreach($opciones as $opcion)
            <label class="block cursor-pointer">
                <input type="radio" name="selected_role" value="{{ $opcion['key'] }}" 
                       class="peer sr-only" {{ $loop->first ? 'checked' : '' }}>
                
                <div class="flex items-center p-4 bg-gray-900 border-2 border-gray-700 rounded-xl transition-all 
                            hover:border-red-500 hover:bg-gray-800 peer-checked:border-red-600 peer-checked:bg-red-900/20">
                    
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center mr-4">
                        <i class="fas {{ $opcion['icon'] }} text-lg text-white"></i>
                    </div>
                    
                    <div class="flex-1">
                        <h3 class="text-white font-semibold text-lg">{{ $opcion['label'] }}</h3>
                        <p class="text-gray-400 text-sm">Acceder al panel de {{ strtolower($opcion['label']) }}</p>
                    </div>

                    <div class="flex-shrink-0 text-red-500 opacity-0 peer-checked:opacity-100 transition-opacity">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
            </label>
            @endforeach

            <!-- Botón Continuar -->
            <button type="submit" class="w-full mt-6 bg-red-700 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-xl transition transform hover:scale-[1.02] shadow-lg shadow-red-900/50">
                <i class="fas fa-sign-in-alt mr-2"></i>Ingresar al Panel
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-6 text-center">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-white text-sm transition">
                    <i class="fas fa-sign-out-alt mr-1"></i>Cerrar sesión y volver al inicio
                </button>
            </form>
        </div>
    </div>

</body>
</html>