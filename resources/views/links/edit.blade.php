<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar enlace</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="mb-4 text-sm text-gray-600">
                    Enlace corto: <span class="font-mono">{{ url($link->code) }}</span>
                </p>

                <form action="{{ route('links.update', $link) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="original_url" value="URL original" />
                        <x-text-input id="original_url" name="original_url" type="url"
                                      class="mt-1 block w-full"
                                      :value="old('original_url', $link->original_url)" required />
                        <x-input-error :messages="$errors->get('original_url')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="expires_at" value="Fecha de expiración (opcional)" />
                        <x-text-input id="expires_at" name="expires_at" type="datetime-local"
                                      class="mt-1 block w-full"
                                      :value="old('expires_at', $link->expires_at?->format('Y-m-d\TH:i'))" />
                        <x-input-error :messages="$errors->get('expires_at')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="max_clicks" value="Límite de clics (opcional)" />
                        <x-text-input id="max_clicks" name="max_clicks" type="number" min="1"
                                      class="mt-1 block w-full"
                                      :value="old('max_clicks', $link->max_clicks)" />
                        <x-input-error :messages="$errors->get('max_clicks')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Guardar cambios</x-primary-button>
                        <a href="{{ route('links.index') }}" class="text-sm text-gray-600">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>