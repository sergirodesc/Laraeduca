<div wire:poll.500ms="loadPendingApprovals" class="py-5">

    <div class="overflow-x-auto">
        <table class="min-w-full w-full text-center">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Empleado
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Semana
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Horas totales
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>

                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($pendingApprovals as $weekInfo)
                    @foreach ($weekInfo['studentsHours'] as $userId => $userHours)
                        <tr class="text-sm font-medium text-gray-900">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $userHours['name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $weekInfo['dateRange'] ?? 'No disponible' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $userHours['totalHours'] }}
                            </td>
                            <td class="py-2 whitespace-nowrap">
                                <span class="px-6 py-4 whitespace-nowrap">
                                    Pendiente
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <button
                                    wire:click="approveWeekSchedules('{{ $weekInfo['startDate'] }}', '{{ $weekInfo['endDate'] }}')"
                                    class="text-zinc-800 material-symbols-outlined hover:text-emerald-500 transition duration-200 ease-in-out">
                                    task_alt
                                </button>
                                <button
                                    wire:click="showWeekSchedules('{{ $weekInfo['startDate'] }}', '{{ $weekInfo['endDate'] }}', '{{ $userId }}')"
                                    class="ml-2 text-zinc-800 material-symbols-outlined hover:text-emerald-500 transition duration-200 ease-in-out">
                                    edit_note
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        @if ($pendingApprovals->isEmpty())
            <div class="text-center py-4 text-md text-gray-600">No hay fichajes pendientes de aprobación</div>
        @endif

    </div>

    @if ($showPendingApprovalModal)
        <x-dialog-modal wire:model="showPendingApprovalModal">
            <x-slot name="title">
                Registros Pendientes de Validación
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
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Opciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dailySchedules as $date => $details)
                                <tr class="shadow">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $date }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="text"
                                            class="form-input {{ $editableScheduleId == $details['schedule_id'] ? 'editable' : '' }}"
                                            wire:model.lazy="temporaryTimes.{{ $details['schedule_id'] }}"
                                            @if ($editableScheduleId != $details['schedule_id']) disabled @endif
                                            wire:keydown.enter="saveTimeOnEnterOrBlur({{ $details['schedule_id'] }})"
                                            wire:blur="saveTimeOnEnterOrBlur({{ $details['schedule_id'] }})">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if (!empty($details['observations']) && count($details['observations']) > 0)
                                            <span wire:click="showObservations('{{ $date }}')"
                                                class="cursor-pointer" title="Ver observaciones">
                                                <span
                                                    class="material-symbols-outlined icon-left-shift hover:text-red-700 transition duration-200 ease-in-out">
                                                    visibility
                                                </span>
                                            </span>
                                        @endif
                                        @if (!$editableScheduleId == $details['schedule_id'])
                                            <span wire:click="enableEditing({{ $details['schedule_id'] }})"
                                                class="cursor-pointer" title="Editar horas">
                                                <span
                                                    class="material-symbols-outlined hover:text-red-700 transition duration-200 ease-in-out">
                                                    edit
                                                </span>
                                            </span>
                                        @endif
                                        @if ($editableScheduleId == $details['schedule_id'])
                                            <span wire:click="disableEditing" class="cursor-pointer"
                                                title="Detener edición">
                                                <span
                                                    class="material-symbols-outlined hover:text-red-700 transition duration-200 ease-in-out">
                                                    close
                                                </span>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-button wire:click="closeModal">
                    Cerrar
                </x-button>
            </x-slot>
        </x-dialog-modal>
    @endif

    @if ($viewingObservationId)
        <x-dialog-modal wire:model="viewingObservationId">
            <x-slot name="title">
                Observaciones
            </x-slot>
            <x-slot name="content">
                <ul>
                    @foreach ($dailySchedules[$viewingObservationId]['observations'] ?? [] as $observation)
                        <li>{{ $observation }}</li>
                    @endforeach
                </ul>
            </x-slot>
            <x-slot name="footer">
                <x-button wire:click="closeObservationModal">
                    Cerrar
                </x-button>
            </x-slot>
        </x-dialog-modal>
    @endif

    <style>
        .scroll-container::-webkit-scrollbar {
            display: none;
        }

        .scroll-container {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .icon-left-shift {
            display: inline-block;
            transform: translateX(-30px);
            /* Ajusta este valor según sea necesario */
        }

        .scroll-container::-webkit-scrollbar {
            display: none;
        }

        .scroll-container {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .editable {
            border: 2px solid #94aa95;
            /* Por ejemplo, un borde verde */
        }
    </style>
</div>
