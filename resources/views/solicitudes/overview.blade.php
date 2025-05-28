<x-app-layout>
    <div class="min-h-screen flex flex-col mx-auto p-6 bg-gray-50">

        <!-- Panel de visualización -->
        <div class="bg-white rounded-xl shadow p-6 mb-6 flex-shrink-0">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">Visualización de Solicitudes</h2>


                <form method="GET" class="mb-6 space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
    <div>
        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de inicio</label>
        <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}"
               class="w-full border border-gray-300 rounded p-2 mt-1">
    </div>
    <div>
        <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha de fin</label>
        <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}"
               class="w-full border border-gray-300 rounded p-2 mt-1">
    </div>
    <div>
        <label for="usuario_id" class="block text-sm font-medium text-gray-700">Usuario</label>
        <select name="usuario_id" id="usuario_id"
                class="w-full border border-gray-300 rounded p-2 mt-1">
            <option value="">Todos</option>
            @foreach ($usuarios as $usuario)
                <option value="{{ $usuario->id }}" @selected(request('usuario_id') == $usuario->id)>
                    {{ $usuario->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="grupo_id" class="block text-sm font-medium text-gray-700">Grupo</label>
        <select name="grupo_id" id="grupo_id"
                class="w-full border border-gray-300 rounded p-2 mt-1">
            <option value="">Todos</option>
            @foreach ($grupos as $grupo)
                <option value="{{ $grupo->id }}" @selected(request('grupo_id') == $grupo->id)>
                    {{ $grupo->nombre }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="flex flex-col md:flex-row gap-3">
        <x-blue-button type="submit" class="w-full md:w-auto" text="Filtrar"/>
        <x-gray-button href="{{ route('solicitudes.overview') }}" class="w-full md:w-auto" text="Quitar filtros"/>
    </div>


</form>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Solicitudes por Tipo</h3>
                    <div class="bg-gray-100 rounded-lg p-4 h-72">
                        <canvas id="chartTipo" class="w-full h-full"></canvas>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Solicitudes por Estado</h3>
                        <div class="bg-gray-100 rounded-lg p-4 h-72">
                            <canvas id="chartEstado" class="w-full h-full"></canvas>
                        </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <x-gray-button href="{{ route('solicitudes.export.excel', request()->query()) }}" text="Exportar a Excel" />
                <x-blue-button href="{{ route('solicitudes.export.csv', request()->query()) }}" text="Exportar a CSV"/>
            </div>
        </div>

    </div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labelsTipo = {!! json_encode($porTipo->keys()) !!};
    const dataTipo = {!! json_encode($porTipo->values()) !!};

    const labelsEstado = {!! json_encode($porEstado->keys()) !!};
    const dataEstado = {!! json_encode($porEstado->values()) !!};

    // Paleta personalizada con tonos Tailwind
const coloresTipo = [
  '#60a5fa', // azul claro (tailwind blue-400)
  '#34d399', // verde menta (tailwind green-400)
  '#fbbf24', // amarillo dorado (tailwind yellow-400)
  '#f87171', // rojo suave (tailwind red-400)
  '#a78bfa', // púrpura lavanda (tailwind purple-400)
  '#38bdf8', // cian brillante (tailwind cyan-400)
  '#f97316', // naranja brillante (tailwind orange-500)
  '#ec4899', // rosa fuerte (tailwind pink-500)
  '#22d3ee', // azul turquesa (tailwind cyan-500)
  '#10b981', // verde esmeralda (tailwind emerald-500)
];
const coloresEstado = [
  '#4ade80', // verde claro (tailwind green-400)
  '#facc15', // amarillo vibrante (tailwind yellow-400)
  '#f87171', // rojo suave (tailwind red-400)
  '#60a5fa', // azul claro (tailwind blue-400)
  '#fb923c', // naranja (tailwind orange-400)
  '#8b5cf6', // violeta (tailwind purple-500)
  '#ec4899', // rosa fuerte (tailwind pink-500)
  '#22c55e', // verde medio (tailwind green-500)
];

    // Gráfico por Tipo
    new Chart(document.getElementById('chartTipo'), {
        type: 'bar',
        data: {
            labels: labelsTipo,
            datasets: [{
                label: 'Cantidad de solicitudes',
                data: dataTipo,
                backgroundColor: coloresTipo,
                borderRadius: 8,
                hoverBackgroundColor: '#1d4ed8'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.formattedValue} solicitudes`;
                        }
                    },
                    backgroundColor: '#1f2937',
                    titleColor: '#fff',
                    bodyColor: '#d1d5db'
                },
                legend: {
                    display: false
                }
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#374151' // texto gris-700
                    }
                },
                x: {
                    ticks: {
                        color: '#374151'
                    }
                }
            }
        }
    });

    // Gráfico por Estado
    new Chart(document.getElementById('chartEstado'), {
        type: 'pie',
        data: {
            labels: labelsEstado,
            datasets: [{
                data: dataEstado,
                backgroundColor: coloresEstado,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.formattedValue || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const porcentaje = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${porcentaje}%)`;
                        }
                    },
                    backgroundColor: '#1f2937',
                    titleColor: '#fff',
                    bodyColor: '#d1d5db'
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#374151'
                    }
                }
            }
        }
    });
</script>
</x-app-layout>
