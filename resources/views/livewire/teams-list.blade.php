<div>
    <div class="flex flex-row justify-between">
        <div class="flex-1">
            <x-input wire:model.live="search" type="text" class="w-full bg-gray-200 text-zinc-500 placeholder-zinc-500 rounded-md shadow-sm border-transparent focus:outline-none focus:ring-transparent focus:border-transparent" name="search" placeholder="Buscar curso..." />
        </div>
        <div class="flex items-center">
            <a href="{{ route('teams.create') }}"
                class="bg-emerald-400/60 w-full sm:w-40 ml-4 mt-0 shadow text-emerald-700 font-medium rounded-lg text-sm py-2.5 px-4 text-center inline-flex items-center transition-colors duration-300 ease-in-out">
                <span class="material-symbols-outlined mr-3">groups</span>
                Crear Curso
            </a>
        </div>
    </div>

    <div class="overflow-x-auto mt-10">
        <table class="min-w-full w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" wire:click="sortBy('name')"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                        Nombre
                        @if ($sortField === 'name')
                            @if ($sortDirection === 'asc')
                                <span>&#9650;</span>
                            @else
                                <span>&#9660;</span>
                            @endif
                        @endif
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fecha de Creación
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Usuarios
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                    </th>
                </tr>
            </thead>

            <tbody class="bg-white">
                @if ($teams->count() > 0)
                    @foreach ($teams as $team)
                        {{--@if (Auth::user()->hasRole('Administrador'))--}}
                            <tr class="shadow">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $team->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-left">
                                    <div class="text-sm text-gray-500">{{ $team->created_at->format('d/m/Y H:i:s') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm text-gray-500">{{ $team->users()->count() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button
                                        class="text-zinc-800 material-symbols-outlined hover:text-emerald-500 transition duration-200 ease-in-out"
                                        onclick="window.location.href = '{{ route('teams.show', $team->id) }}'">
                                        folder_managed
                                    </button>
                                </td>
                            </tr>
                        {{--@endif--}}
                    @endforeach
                @else
                    <tr>
                        <td colspan="12" class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-500">No hay equipos disponibles</p>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="mt-4">
            {{ $teams->links() }}
        </div>
    </div>
</div>