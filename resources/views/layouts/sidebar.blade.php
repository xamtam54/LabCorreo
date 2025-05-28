<aside class="fixed top-16 left-0 h-[calc(100vh-4rem)] bg-white border-r shadow-xl flex flex-col z-10
             w-16
             sm:w-64
             px-1
             sm:px-6
             py-8
             transition-all duration-300">

    <div class="text-blue-900 font-semibold text-2xl mb-8 text-center
                hidden sm:block">
        Sistema Integral de Gestión de Solicitudes
    </div>

    <nav class="flex flex-col gap-2 text-gray-800">
        <a href="{{ route('solicitudes.soloPrioridad')}}" class="flex items-center justify-center sm:justify-start gap-3 px-2 sm:px-4 py-2 rounded-lg hover:bg-blue-100 hover:text-blue-900 transition-all">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75h-5.25v-6h-6v6H3.75A.75.75 0 013 21V9.75z" />
            </svg>
            <span class="hidden sm:inline">Dashboard</span>
        </a>

        <a href="{{ route('grupos.unirse') }}" class="flex items-center justify-center sm:justify-start gap-3 px-2 sm:px-4 py-2 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
            </svg>
            <span class="hidden sm:inline">Grupos</span>
        </a>

        <a href="{{ route('solicitudes.overview') }}" class="flex items-center justify-center sm:justify-start gap-3 px-2 sm:px-4 py-2 rounded-lg hover:bg-blue-100 hover:text-blue-900 transition-all">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6m4 6V7m4 10v-3M3 3v18h18" />
            </svg>
            <span class="hidden sm:inline">Reportes</span>
        </a>
    </nav>
</aside>
