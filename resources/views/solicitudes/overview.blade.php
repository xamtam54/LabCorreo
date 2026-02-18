<x-app-layout>
    <div class="min-h-screen flex flex-col mx-auto p-6 bg-gray-50">

        <div class="bg-white rounded-xl shadow p-6 mb-6 flex-shrink-0">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">
                Panel de control — Solicitudes
            </h2>

            {{-- Filtros --}}
            <form method="GET" class="mb-6 space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}"
                        class="w-full border rounded p-2 mt-1">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de fin</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}"
                        class="w-full border rounded p-2 mt-1">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Usuario</label>
                    <select name="usuario_id" class="w-full border rounded p-2 mt-1">
                        <option value="">Todos</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}" @selected(request('usuario_id') == $u->id)>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Grupo</label>
                    <select name="grupo_id" class="w-full border rounded p-2 mt-1">
                        <option value="">Todos</option>
                        @foreach ($grupos as $g)
                            <option value="{{ $g->id }}" @selected(request('grupo_id') == $g->id)>
                                {{ $g->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col md:flex-row gap-3">
                    <x-blue-button type="submit" text="Aplicar filtros"/>
                    <x-gray-button href="{{ route('solicitudes.overview') }}" text="Reiniciar"/>
                </div>
            </form>

            {{-- KPIs --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="p-4 bg-gray-50 rounded border">
                    <p class="text-sm text-gray-600">Total de solicitudes</p>
                    <p class="text-2xl font-semibold">{{ $totales }}</p>
                </div>

                <div class="p-4 bg-gray-50 rounded border">
                    <p class="text-sm text-gray-600">Solicitudes vencidas</p>
                    <p class="text-2xl font-semibold">{{ $vencidas }}</p>
                </div>

                <div class="p-4 bg-gray-50 rounded border">
                    <p class="text-sm text-gray-600">Porcetaje de solicitudes vencidas</p>
                    <p class="text-2xl font-semibold text-{{ $porcentajeVencidas > 20 ? 'red-600' : 'green-600' }}">
                        {{ number_format($porcentajeVencidas, 2) }}%
                    </p>
                </div>
            </div>

            {{-- Gráficas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                {{-- 1) Tendencia mensual --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Tendencia mensual — Solicitudes</h3>
                    <div class="bg-gray-100 rounded-lg p-4 h-72">
                        <canvas id="chartMes"></canvas>
                    </div>
                </div>

                {{-- 2) Donut: vencidas vs no vencidas --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Fuera de plazo vs Dentro de plazo</h3>
                    <div class="bg-gray-100 rounded-lg p-4 h-72 flex items-center justify-center">
                        <canvas id="chartDonut" class="max-h-56"></canvas>
                    </div>
                </div>

            </div>

            {{-- Botones de exportación --}}
            <div class="flex flex-col sm:flex-row gap-4 mt-6">
                <x-gray-button href="{{ route('solicitudes.export.excel', request()->query()) }}" text="Exportar Excel"/>
                <x-blue-button href="{{ route('solicitudes.export.csv', request()->query()) }}" text="Exportar CSV"/>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const labelsMes = {!! json_encode(array_keys($porMes->toArray())) !!};
        const dataMes   = {!! json_encode(array_values($porMes->toArray())) !!};

        const donutData = {!! json_encode($donutData) !!};
        const donutLabels = ['Vencidas', 'Dentro del plazo'];

        const palette = {
            indigo: '#1e3a8a',
            indigoFill: 'rgba(30,58,138,0.08)',
            red: '#dc2626',
            green: '#059669'
        };

        // Formatear YYYY-MM → Mes Año
        function formatMonth(ym) {
            const [y, m] = ym.split('-');
            return new Date(y, m - 1, 1)
                .toLocaleDateString('es-CO', { month: 'short', year: 'numeric' });
        }

        const labelsMesDisplay = labelsMes.map(formatMonth);

        // Línea: Solicitudes por mes
        new Chart(document.getElementById('chartMes'), {
            type: 'line',
            data: {
                labels: labelsMesDisplay,
                datasets: [{
                    label: 'Solicitudes',
                    data: dataMes,
                    borderColor: palette.indigo,
                    backgroundColor: palette.indigoFill,
                    fill: true,
                    tension: 0.25,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // Donut: Vencidas / No vencidas
        new Chart(document.getElementById('chartDonut'), {
            type: 'doughnut',
            data: {
                labels: donutLabels,
                datasets: [{
                    data: donutData,
                    backgroundColor: [palette.red, palette.green]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>

</x-app-layout>
