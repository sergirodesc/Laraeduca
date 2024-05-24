<div>
    {{-- * Filters Section --}}
    <section class="flex flex-col lg:flex-row justify-end sm:justify-between">
        <div class="flex-1">
            <input wire:model.live="search" type="text"
                class="w-full bg-gray-200 text-zinc-500 placeholder-zinc-500 rounded-md shadow-sm border-transparent focus:outline-none focus:ring-transparent focus:border-transparent"
                name="search" placeholder="Buscar usuario..." />
        </div>
        <div class="flex items-center lg:justify-start sm:justify-between flex-col sm:flex-row lg:mt-0 mt-2">
            <select wire:model.live="status" id="status" name="status"
                class="bg-gray-200 lg:ml-4 w-full sm:flex-1 sm:mr-4 lg:mr-0 sm:w-32 border-transparent focus:outline-none focus:ring-transparent focus:border-transparent shadow text-zinc-500 text-sm rounded-lg block py-2.5 px-5 transition-colors duration-300 ease-in-out">
                <option value="">Estado</option>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>

            {{-- *if (Auth::user()->hasRole('Administrador')) --}}
            <select wire:model.live="perPage"
                class="bg-gray-200 lg:ml-4 w-full mt-2 sm:mt-0 flex-1 sm:w-32 border-transparent shadow text-zinc-500 text-sm rounded-lg focus:outline-none focus:ring-transparent focus:border-transparent block py-2.5 px-5 transition-colors duration-300 ease-in-out">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>

            <button type="button" wire:click="openModal"
                class="bg-green-500/60 w-full sm:w-40 sm:ml-4 mt-2 sm:mt-0 shadow text-black-700 font-medium rounded-lg text-sm py-2.5 px-4 text-center inline-flex items-center transition-colors duration-300 ease-in-out">
                <span class="material-symbols-outlined mr-3">person_add</span>
                Create user
            </button>


            {{-- *endif --}}
        </div>
    </section>

    {{-- * Users Table --}}
    <div class="overflow-x-auto mt-10">
        <table class="min-w-full w-full">
            <thead class=" bg-gray-700">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                    </th>

                    <th wire:click="sortBy('id')"
                        class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider cursor-pointer">
                        ID
                        @if ($sortField === 'id')
                            @if ($sortDirection === 'asc')
                                <span>&#9650;</span>
                            @else
                                <span>&#9660;</span>
                            @endif
                        @endif
                    </th>

                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                        Nombre
                    </th>

                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                        Email
                    </th>

                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                        Rol
                    </th>

                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                        Fecha de Registro
                    </th>

                    <th scope="col" class="px-6 py-3 text-xs font-medium text-white uppercase tracking-wider">
                        Estado
                    </th>

                    <th scope="col" class="px-6 py-3 text-xs font-medium text-white uppercase tracking-wider">

                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-300 text-white bg-gray-700">
                @if ($users->count() > 0)
                    @foreach ($users as $user)
                        <tr class="shadow">
                            <td class="px-4 py-4 whitespace-nowrap w-16">
                                @if ($user->profile_photo_url)
                                    <img class="h-auto w-auto rounded-full object-cover ml-5"
                                        src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
                                @else
                                    <span
                                        class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center text-white">
                                        {{ substr($user->name, 0, 1) }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-white">{{ $user->id }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">
                                    {{ $user->name }}
                                </div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-white">{{ $user->email }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-white">
                                    @if ($user->roles)
                                        @if ($user->roles->count() > 0)
                                            {{ $user->roles->first()->name }}
                                        @else
                                            Sin Rol
                                        @endif
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-white">{{ $user->created_at->format('d/m/Y H:i:s') }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span
                                    class="inline-flex items-center text-xs font-medium px-2.5 py-0.5 rounded-full {{ $user->status == '1' ? 'bg-green-400/20 text-green-500' : 'bg-red-400/20 text-red-500' }}">
                                    <span
                                        class="w-2 h-2 me-1 rounded-full {{ $user->status == '1' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ $user->status == '1' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                {{-- *if (Auth::user()->hasAnyRole(['Administrador', 'Cliente'])) --}}
                                {{-- <button type="button" wire:click="openUserPermissions({{ $user->id }})"
                                    class="text-black-600 material-symbols-outlined hover:text-red-700 transition duration-200 ease-in-out">
                                    folder_managed
                                </button> --}}
                                {{-- *endif --}}
                                <button wire:click="assignTeam({{ $user->id }})"
                                    class="text-white material-symbols-outlined hover:text-emerald-500 transition duration-200 ease-in-out">
                                    edit
                                </button>
                                <button type="button" wire:click="$set('managingFiles', {{ $user->id }})"
                                    class="text-white material-symbols-outlined hover:text-emerald-500 transition duration-200 ease-in-out">
                                    description
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="12" class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-white">No hay usuarios disponibles</p>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

    </div>
    <div class="mt-4">
        {{ $users->links() }}
    </div>

    {{-- * Create User Modal --}}
    <x-dialog-modal wire:model="isModalOpen" class="transform transition-transform ease-in-out duration-300 left-0 ">
        <x-slot name="title">
            <div class="text-emerald-500">
                Crear Usuario
            </div>
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="createUser ">
                <div class="mb-4 ">
                    <label for="name" class="block text-sm font-medium">Nombre</label>
                    <input type="text" wire:model="newUser.name" id="name"
                        class="w-full bg-gray-200 text-zinc-500 placeholder-zinc-500 rounded-md shadow-sm border-transparent focus:outline-none focus:ring-transparent focus:border-transparent">
                    @error('newUser.name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input type="email" wire:model="newUser.email" id="email"
                        class="w-full bg-gray-200 text-zinc-500 placeholder-zinc-500 rounded-md shadow-sm border-transparent focus:outline-none focus:ring-transparent focus:border-transparent">
                    @error('newUser.email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium">Contraseña</label>
                    <input type="password" wire:model="newUser.password" id="password"
                        class="w-full bg-gray-200 text-zinc-500 placeholder-zinc-500 rounded-md shadow-sm border-transparent focus:outline-none focus:ring-transparent focus:border-transparent">
                    @error('newUser.password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </form>
        </x-slot>

        <x-slot name="footer" class="flex items-center justify-end mt-4 bg-gray-200">
            <button
                class="inline-flex mr-2 items-center border border-transparent font-semibold text-xs uppercase tracking-widest hover:bg-emerald-600 active:bg-zinc-900 focus:outline-none focus:border-emerald-900 focus:ring focus:ring-emerald-300 disabled:opacity-25 transition bg-emerald-500 text-emerald-900 px-4 py-2 rounded-md"
                wire:click="createUser" wire:loading.attr="disabled">
                Crear
            </button>
            <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    {{-- Edit User --}}
    @if ($isTeamModalOpen)
        <x-dialog-modal wire:model="isTeamModalOpen">
            <x-slot name="title">
                Asignar Equipo a {{ $selectedUser['name'] ?? '' }}
            </x-slot>

            <x-slot name="content">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 sm:text-sm"
                        type="text" id="name" name="name" wire:model="selectedUser.name">
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 sm:text-sm"
                        type="email" id="email" name="email" wire:model="selectedUser.email">
                </div>
                {{--@if (Auth::user()->hasRole('Administrador'))--}}
                    <div class="mb-4">
                        <label for="team" class="block text-sm font-medium text-gray-700">Asignar nuevo
                            equipo</label>
                        <select wire:model="selectedTeam" id="team" name="team"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 sm:text-sm">
                            <option value="">Seleccionar Equipo</option>
                            @foreach ($teams as $teamId => $teamName)
                                <option value="{{ $teamId }}">{{ $teamName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="user_status" class="block text-sm font-medium text-gray-700">Estado del
                            Usuario</label>
                        <select wire:model="selectedUser.status" id="user_status" name="user_status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 sm:text-sm">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                {{--@endif--}}
                <div class="mb-4">
                    <label for="spatie_role" class="block text-sm font-medium text-gray-700">Rol</label>
                    <select wire:model="selectedSpatieRole" id="spatie_role" name="spatie_role"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 sm:text-sm">
                        <option value="">Seleccionar Rol</option>
                        @foreach ($spatieRoles as $spatieRole)
                            @if (Auth::user()->hasRole('Cliente') && $spatieRole->name === 'Administrador')
                            @else
                                <option value="{{ $spatieRole->name }}">{{ $spatieRole->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

            </x-slot>

            <x-slot name="footer" class="flex items-center justify-end mt-4">
                <x-secondary-button wire:click="closeTeamModal" wire:loading.attr="disabled">
                    Cancelar
                </x-secondary-button>

                <x-button class="ml-2" wire:click="saveTeam" wire:loading.attr="disabled">
                    Asignar
                </x-button>
            </x-slot>
        </x-dialog-modal>
    @endif

    {{-- * Manage User Files Modal --}}
    @if ($managingFiles)
        <x-dialog-modal wire:model="managingFiles"
            class="transform transition-transform ease-in-out duration-300 left-0">
            <x-slot name="title">
                Cargar Documento
            </x-slot>

            <x-slot name="content">

                <form wire:submit.prevent="saveFile">
                    <div class="form-group">
                        <x-label for="document_type" class="">Tipo de Documento</x-label>
                        <input type="text" wire:model.defer="documentType"
                            class="form-control bg-gray-200 text-zinc-400 mt-1 block w-full border-transparent rounded-md shadow-sm focus:outline-none focus:ring-transparent focus:border-transparent sm:text-sm"
                            id="document_type" />
                    </div>

                    <div class="form-group mt-4">
                        <x-label for="expiry_date" class="">Fecha de Entrega</x-label>
                        <input type="date" wire:model.defer="expiryDate"
                            class="form-control bg-gray-200 text-zinc-400 mt-1 block w-full border-transparent rounded-md shadow-sm focus:outline-none focus:ring-transparent focus:border-transparent sm:text-sm"
                            id="expiry_date" />
                    </div>

                    <div class="form-group mt-4">

                        <label class="flex items-center cursor-pointer">
                            Subir archivos <span class="material-symbols-outlined ml-2">upload_file</span>
                            <input type="file" class="hidden" wire:model="file" />
                        </label>
                        @error('file')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-row justify-end mt-4">
                        <button
                            class="inline-flex mr-2 items-center border border-transparent font-semibold text-xs uppercase tracking-widest hover:bg-emerald-600 active:bg-zinc-900 focus:outline-none focus:border-emerald-900 focus:ring focus:ring-emerald-300 disabled:opacity-25 transition bg-emerald-500 text-emerald-900 px-4 py-2 rounded-md"><span
                                class="material-symbols-outlined mr-2">upload_file</span>Subir</button>
                        <x-secondary-button wire:click="$toggle('managingFiles')"><span
                                class="material-symbols-outlined mr-2">file_upload_off</span>Cancelar</x-secondary-button>
                    </div>
                </form>
            </x-slot>

            <x-slot name="footer" class="flex flex-col bg-gray-200">
                {{-- * User Files Table --}}
                <div class="flex flex-col w-full">

                    @if (!empty($publicFiles))
                        <div class="overflow-x-auto">
                            <table class="min-w-full w-full text-center overflow-x-auto">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider">
                                            Tipo de documento
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider">
                                            Fecha de Entrega
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">

                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-800">
                                    @foreach ($documents as $file)
                                        <tr class="shadow">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $file->document_type }} <span
                                                    class="text-xs text-emerald-500">({{ $this->getFileSize($file->file_path) }})</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $file->updated_at }}
                                            </td>
                                            <td
                                                class="border-t-0 px-6 align-middle border-l-0 text-right border-r-0 whitespace-nowrap p-2">
                                                <button type="button"
                                                    wire:click="download('{{ $file->file_path }}')"
                                                    class="text-black-600 material-symbols-outlined hover:text-yellow-500 transition duration-200 ease-in-out">
                                                    download
                                                </button>
                                                <button type="button"
                                                    wire:click="deleteFile('{{ $file->file_path }}')"
                                                    class="text-black-600 material-symbols-outlined hover:text-yellow-500 transition duration-200 ease-in-out">
                                                    delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $documents->links() }}
                        </div>
                    @else
                        <div class="w-full mt-4 flex justify-center">
                            <div class="container">
                                <div class="title">Sin archivos disponibles...</div>
                            </div>
                        </div>
                    @endif
                </div>
            </x-slot>
        </x-dialog-modal>
    @endif
</div>
