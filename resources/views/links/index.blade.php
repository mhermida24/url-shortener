<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis enlaces</h2>
            <a href="{{ route('links.create') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">
                Nuevo enlace
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enlace corto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL original</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clics</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($links as $link)
                            <tr>
                                <td class="px-4 py-3 text-sm font-mono">{{ url($link->code) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">
                                    {{ $link->original_url }}
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $link->clicks_count }}</td>
                                <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                                    <a href="{{ route('links.show', $link) }}" class="text-blue-600">Ver</a>
                                    <a href="{{ route('links.edit', $link) }}" class="ml-3 text-yellow-600">Editar</a>
                                    <form action="{{ route('links.destroy', $link) }}" method="POST"
                                          class="inline"
                                          onsubmit="return confirm('¿Eliminar este enlace?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="ml-3 text-red-600">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                    Todavía no creaste ningún enlace.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $links->links() }}</div>
        </div>
    </div>
</x-app-layout>