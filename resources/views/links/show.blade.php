<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle del enlace</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-3">
                <p><strong>Enlace corto:</strong> <span class="font-mono">{{ url($link->code) }}</span></p>
                <p><strong>URL original:</strong> {{ $link->original_url }}</p>
                <p><strong>Clics:</strong> {{ $link->clicks_count }}</p>
                <p><strong>Expira:</strong> {{ $link->expires_at?->format('d/m/Y H:i') ?? 'Nunca' }}</p>
                <p><strong>Límite de clics:</strong> {{ $link->max_clicks ?? 'Sin límite' }}</p>
                <p><strong>Creado:</strong> {{ $link->created_at->format('d/m/Y H:i') }}</p>

                <a href="{{ route('links.index') }}" class="inline-block text-sm text-blue-600">← Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>