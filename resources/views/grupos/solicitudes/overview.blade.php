<x-app-layout>
    <div class="p-6 bg-white rounded-xl shadow">
        <h2 class="text-2xl font-semibold mb-6">Visualización de Solicitudes</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold mb-2">Solicitudes por Tipo</h3>
                <canvas id="chartTipo"></canvas>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-2">Solicitudes por Estado</h3>
                <canvas id="chartEstado"></canvas>
            </div>
        </div>

        {{-- Botones de exportación --}}
        <div class="mt-6 space-x-4">
            <a href="{{ route('solicitudes.export.excel') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                Exportar Solicitudes a Excel
            </a>

            <a href="{{ route('solicitudes.export.csv') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Exportar Solicitudes a CSV
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labelsTipo = {!! json_encode($porTipo->keys()) !!};
        const dataTipo = {!! json_encode($porTipo->values()) !!};

        const labelsEstado = {!! json_encode($porEstado->keys()) !!};
        const dataEstado = {!! json_encode($porEstado->values()) !!};

        new Chart(document.getElementById('chartTipo'), {
            type: 'bar',
            data: {
                labels: labelsTipo,
                datasets: [{
                    label: 'Cantidad',
                    data: dataTipo,
                    backgroundColor: 'rgba(59, 130, 246, 0.6)'
                }]
            },
        });

        new Chart(document.getElementById('chartEstado'), {
            type: 'pie',
            data: {
                labels: labelsEstado,
                datasets: [{
                    data: dataEstado,
                    backgroundColor: ['#4ade80', '#facc15', '#f87171', '#60a5fa']
                }]
            },
        });
    </script>
</x-app-layout>
