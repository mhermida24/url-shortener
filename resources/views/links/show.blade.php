<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Estadísticas del enlace</h2>
            <a href="{{ route('links.index') }}" class="text-sm text-blue-600">← Volver</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Datos del enlace --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-2">
                <p><strong>Enlace corto:</strong> <span class="font-mono">{{ url($link->code) }}</span></p>
                <p class="truncate"><strong>URL original:</strong> {{ $link->original_url }}</p>
                <p><strong>Expira:</strong> {{ $link->expires_at?->format('d/m/Y H:i') ?? 'Nunca' }}</p>
                <p><strong>Límite de clics:</strong> {{ $link->max_clicks ?? 'Sin límite' }}</p>
                <p><strong>Creado:</strong> {{ $link->created_at->format('d/m/Y H:i') }}</p>
            </div>

            {{-- Total --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500">Clics totales</p>
                <p class="text-4xl font-bold">{{ $total }}</p>
            </div>

            {{-- Clics por día --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Clics por día (últimos 30 días)</h3>
                <div class="h-64">
                    <canvas id="chartPerDay"></canvas>
                </div>
            </div>

            {{-- Navegadores, SO, dispositivos --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold mb-4">Navegadores</h3>
                    <div class="h-56"><canvas id="chartBrowsers"></canvas></div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold mb-4">Sistemas operativos</h3>
                    <div class="h-56"><canvas id="chartOs"></canvas></div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold mb-4">Dispositivos</h3>
                    <div class="h-56"><canvas id="chartDevices"></canvas></div>
                </div>
            </div>

            {{-- Orígenes --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Principales orígenes</h3>
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($referers as $row)
                            <tr>
                                <td class="py-2 truncate max-w-md">{{ $row->label }}</td>
                                <td class="py-2 text-right font-semibold">{{ $row->total }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-2 text-gray-500">Sin datos todavía.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const perDay = @json($perDay);
        const browsers = @json($browsers);
        const systems = @json($systems);
        const devices = @json($devices);

        new Chart(document.getElementById('chartPerDay'), {
            type: 'line',
            data: {
                labels: perDay.labels,
                datasets: [{
                    label: 'Clics',
                    data: perDay.values,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.15)',
                    fill: true,
                    tension: 0.3,
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });

        function doughnut(id, rows) {
            new Chart(document.getElementById(id), {
                type: 'doughnut',
                data: {
                    labels: rows.map(r => r.label),
                    datasets: [{
                        data: rows.map(r => r.total),
                        backgroundColor: ['#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#7c3aed'],
                    }],
                },
                options: { maintainAspectRatio: false },
            });
        }

        doughnut('chartBrowsers', browsers);
        doughnut('chartOs', systems);
        doughnut('chartDevices', devices);
    </script>
</x-app-layout>