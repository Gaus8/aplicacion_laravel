<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navegación Superior -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
            <span class="text-xl font-bold text-gray-800">Sistema Auth</span>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-700">
                    Hola, <strong class="text-gray-900">{{ auth()->user()->name }}</strong>
                </span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition duration-200">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal del Dashboard -->
    <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <!-- Tarjeta de Bienvenida e Info del Usuario -->
        <div class="bg-white p-8 rounded-xl shadow-md border border-gray-200 mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Panel de Control (Dashboard)</h1>
            <p class="text-gray-600 mb-6">
                ¡Has iniciado sesión con éxito! Esta es una vista protegida mediante el middleware <code class="bg-gray-100 px-2 py-1 rounded text-red-600">auth</code>.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="text-sm font-semibold text-blue-800 uppercase tracking-wider">Nombre del Usuario</h3>
                    <p class="text-lg font-bold text-blue-900 mt-1">{{ auth()->user()->name }}</p>
                </div>
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h3 class="text-sm font-semibold text-green-800 uppercase tracking-wider">Correo Registrado</h3>
                    <p class="text-lg font-bold text-green-900 mt-1">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>

        <!-- Galería del Dashboard -->
        <div class="bg-white p-8 rounded-xl shadow-md border border-gray-200">
            
            <!-- Encabezado de la Galería con el Botón de Subir -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <h2 class="text-2xl font-bold text-gray-800">Galería del dashboard</h2>
                
                <a href="{{ route('admin.media.subirImagen') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Subir Nueva Imagen
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach($media as $item)
                    <article class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col justify-between shadow-sm">
                        <div>
                            <!-- Imagen enlazada para abrir el archivo original en otra pestaña al hacer clic -->
                            <a href="{{ Storage::url($item->path) }}" target="_blank">
                                <img src="{{ Storage::url($item->path) }}" 
                                     alt="{{ $item->name }}" 
                                     class="w-full h-48 object-cover rounded-lg mb-3 border border-gray-200 hover:opacity-90 transition">
                            </a>
                            
                            <!-- Solo muestra el nombre -->
                            <h3 class="text-base font-semibold text-gray-800 text-center truncate" title="{{ $item->name }}">
                                {{ $item->name }}
                            </h3>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

    </main>

</body>
</html>