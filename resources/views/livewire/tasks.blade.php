<div>
    <div class="flex justify-end">
        <button wire:click="$set('showingTaskModal', true)"
            class="bg-green-600/60 w-full sm:w-40 sm:ml-4 mt-2 sm:mt-0 shadow text-black font-medium rounded-lg text-sm py-2.5 px-4 text-center inline-flex items-center transition-colors duration-300 ease-in-out">
            <span class="material-symbols-outlined mr-3">task</span>
            Agregar Tarea
        </button>
    </div>

    <x-dialog-modal wire:model="showingTaskModal">
        <x-slot name="title">
            Agregar Tarea
        </x-slot>

        <x-slot name="content">
            <div class="mt-4">
                <x-label for="taskName" value="Nombre de la Tarea" />
                <x-input id="taskName" type="text" class="block w-full mt-1" wire:model.defer="taskName" />
                <x-input-error for="taskName" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-label for="description" value="Descripción de la Tarea" />
                <x-input id="description" type="text" class="block w-full mt-1" wire:model.defer="description" />
                <x-input-error for="description" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-label for="selectedTeam" value="Seleccionar Equipo" />
                <select id="selectedTeam" wire:model.defer="selectedTeam" class="block w-full mt-1">
                    <option value="">Seleccionar curso...</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
                <x-input-error for="selectedTeam" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <button wire:click="saveTask()"
                class="inline-flex mr-2 items-center border border-transparent font-semibold text-xs uppercase tracking-widest hover:bg-emerald-600 active:bg-zinc-900 focus:outline-none focus:border-emerald-900 focus:ring focus:ring-emerald-300 disabled:opacity-25 transition bg-emerald-500 text-emerald-900 px-4 py-2 rounded-md">
                Guardar
            </button>
            <x-secondary-button wire:click="$set('showingTaskModal', false)">
                Cancelar
            </x-secondary-button>


        </x-slot>
    </x-dialog-modal>

    <div class="grid grid-cols-3 gap-4 mt-4">
        @foreach ($tasks as $task)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-bold">{{ $task->name }}</h2>
                    <p class="text-gray-500 mt-2">Curso: {{ $task->team->name }}</p>
                    <p class="text-gray-500 mt-2"> {{ $task->description }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>


{{-- <div>
    
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg w-2/6 mr-4 h-60 hover:scale-105 transition duration-200 ease-it-out">
        <a href="{{ route('music-task') }}" :active="request()->routeIs('music-task')">
        <p class="flex mt-2 ml-2"><span class="material-symbols-outlined mr-2">music_note</span>
             Music Task
        </p>
        <div class="flex justify-center mt-2">
            <img src="{{asset('assets/music-img.png')}}" alt="music task">
        </div>
    </a>
    </div>

</div> --}}
