<div wire:poll.1000ms="loadStudentSchedules" class="py-5">
    <div class="max-w-full ">
        @if ($onwork)
            <div class="mb-5 flex w-full items-center gap-4">
                <div class="bg-gray-50 border border-green-500 text-green-500 shadow p-3 flex-1 rounded-lg">
                    Jornada ya iniciada. Pulsa el botón para finalizar tu jornada.
                </div>

                <button wire:click="toggleWorkday"
                    class="bg-gray-50 ml-4 border shadow border-gray-300 text-gray-900 focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 font-medium rounded-lg text-sm py-3 px-4 text-center inline-flex items-center transition-colors duration-300 ease-in-out">
                    <span class="material-symbols-outlined mr-3">work_history</span>
                    Parar jornada
                </button>
            </div>
        @else
            <div class="mb-5 flex w-full items-center gap-4">
                <div class="bg-gray-50 border border-red-500 text-red-500 shadow p-3 flex-1 rounded-lg">
                    Todavía no has iniciado tu jornada. Pulsa el botón para comenzar.
                </div>

                <button wire:click="toggleWorkday"
                    class="bg-green-600/60 w-full sm:w-40 sm:ml-4 mt-2 sm:mt-0 shadow text-black-700 font-medium rounded-lg text-sm py-2.5 px-4 text-center inline-flex items-center transition-colors duration-300 ease-in-out">
                    <span class="material-symbols-outlined mr-2">work_history</span>
                    Iniciar asistencia
                </button>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row">
            <div class="bg-white overflow-hidden sm:rounded-lg mr-2">
                <div class="flex flex-col xl:flex-row p-7 gap-20">
                    <section class="flex flex-col gap-3 w-full xl:max-w-[400px]">
                        <p class="text-md text-gray-600">
                            Estado actual: <span class="text-red-400">Pendiente de validación</span>
                        </p>

                        <p class="text-md text-gray-600">
                            Reportes pendientes:
                            <span class="text-green-400 hover:text-green-500">
                                @if ($pendingApprovalCount == 1)
                                    {{ $pendingApprovalCount }} registro pendiente
                                @else
                                    {{ $pendingApprovalCount }} registros pendientes
                                @endif
                            </span>
                        </p>
                    </section>
                </div>
            </div>
            <section class="flex-1 block">
                <div class="block w-full overflow-x-auto">
                    <table class="min-w-full w-full text-center">
                        <thead class="bg-gray-800">
                            <tr>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                                    Día
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                                    Núm Registros
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                                    Horas totales
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                                    Entrada
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                                    Salida
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($studentSchedules as $date => $dayInfo)
                                <tr class="shadow">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $date }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $dayInfo['total'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $dayInfo['hours'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $dayInfo['first_start_time'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $dayInfo['last_end_time'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button wire:click="verifyHours('{{ $date }}')"
                                            class="text-zinc-800 material-symbols-outlined hover:text-emerald-500 transition duration-200 ease-in-out"
                                            type="button">
                                            edit_note
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>


    @if ($showPendingApprovalModal)
        <x-dialog-modal wire:model="showPendingApprovalModal">
            <x-slot name="title">
                Registros Pendientes de Aprobación
            </x-slot>
            <x-slot name="content">
                <div class="overflow-x-auto">
                    <table class="min-w-full w-full text-center">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Horas totales
                                </th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">

                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingApprovals as $item)
                                <tr class="shadow">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $item['date'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $item['hours'] }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <button wire:click="verifyHours('{{ $item['date'] }}')"
                                            class="material-symbols-outlined mt-2 hover:text-emerald-700 transition duration-200 ease-in-out"
                                            type="button">
                                            edit_note
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-button wire:click="closePendingApprovalModal">
                    Cerrar
                </x-button>
            </x-slot>
        </x-dialog-modal>
    @endif

    @if ($showConfirmationModal)
        <x-dialog-modal wire:model="showConfirmationModal">
            <x-slot name="title">
                Confirmación
            </x-slot>
            <x-slot name="content">
                <p>¿Estás seguro de que quieres aprobar todas las horas del día y cerrar la jornada?</p>
            </x-slot>
            <x-slot name="footer">
                <x-button wire:click="approveAllUserSchedules">
                    Sí, aprobar todos
                </x-button>
                <x-secondary-button wire:click="$set('showConfirmationModal', false)" class="ml-2">
                    Cancelar
                </x-secondary-button>
            </x-slot>
        </x-dialog-modal>
    @endif

    @if ($showVerificationModal)
        <x-dialog-modal wire:model="showVerificationModal">
            <x-slot name="title">
                Verificación de Horas
            </x-slot>
            <x-slot name="content">
                <div class="overflow-x-auto scroll-container" style="max-height: 400px;">
                    <table class="min-w-full w-full text-center">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hora de inicio
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hora de fin
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tiempo total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($allUserSchedules->sortByDesc('start_time') as $userSchedule)
                                <tr class="shadow">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $userSchedule->start_time ? $userSchedule->start_time->format('H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $userSchedule->end_time ? $userSchedule->end_time->format('H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $userSchedule->end_time ? $userSchedule->start_time->diff($userSchedule->end_time)->format('%H:%I:%S') : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <label for="observations" class="text-sm font-medium">Observaciones</label>
                    <textarea wire:model="observations" id="observations"
                        class="form-textarea border border-gray-300 w-full resize-none mt-2 h-24 rounded-lg p-2"></textarea>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="text-lg font-semibold mr-auto">
                    Tiempo total del día: {{ $totalHoursOfDay }}
                </div>

                <div class="mr-3">
                    <x-button wire:click="confirmApproveAll">
                        Aprobar todos
                    </x-button>
                    <x-secondary-button wire:click="$set('showVerificationModal', false)" class="ml-2">
                        Cancelar
                    </x-secondary-button>
                </div>

            </x-slot>
        </x-dialog-modal>
    @endif

    <style>
        .scroll-container {
            -ms-overflow-style: none;
            /* Internet Explorer 10+ */
            scrollbar-width: none;
            /* Firefox */
            overflow-y: scroll;
        }

        .scroll-container::-webkit-scrollbar {
            display: none;
            /* Safari y Chrome */
        }
    </style>

</div>