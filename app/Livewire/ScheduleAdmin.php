<?php
namespace App\Livewire;

use App\Models\StudentScheduleModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ScheduleAdmin extends Component
{
    public $pendingApprovals;
    public $showPendingApprovalModal = false;
    public $allUserSchedules;
    public $totalHoursOfDay;

    public $dailySchedules = [];
    public $viewingObservationId = null;
    public $showObservationsModal = false;

    public $selectedWeekSchedules = [];
    public $selectedWeek = '';
    public $editableScheduleId = null;

    public $temporaryTimes = [];

    public function mount()
    {
        $this->allUserSchedules = collect();
        $this->temporaryTimes = []; 
        $this->loadPendingApprovals();
    }

    public function closeModal()
    {
        $this->showPendingApprovalModal = false;
    }

    public function closeObservationModal()
    {
        $this->viewingObservationId = null;
    }

    public function enableEditing($scheduleId)
    {
        $this->editableScheduleId = $scheduleId;
    }

    public function disableEditing()
    {
        $this->editableScheduleId = null;
    }

    public function showWeekSchedules($startDate, $endDate, $userId)
    {
        $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

        $startFormatted = $start->format('Y-m-d H:i:s');
        $endFormatted = $end->format('Y-m-d H:i:s');

        $schedules = StudentScheduleModel::where('user_id', $userId)
            ->whereBetween('start_time', [$startFormatted, $endFormatted])
            ->where('admin_approval', false)
            ->with('user')
            ->get();
        // Agrupa por fecha.
        $this->dailySchedules = $schedules->groupBy(function ($schedule) {
            return Carbon::parse($schedule->start_time)->format('Y-m-d');
        })->map(function ($schedulesOnDate, $date) {
            $firstSchedule = $schedulesOnDate->first();
            $scheduleId = $firstSchedule ? $firstSchedule->id : null;
            $observationsByUser = DB::table('user_daily_records')
                ->whereIn('user_id', $schedulesOnDate->pluck('user_id')->unique())
                ->whereBetween('date_of_day', [$date, $date])
                ->whereNotNull('observations')
                ->get()
                ->groupBy('user_id')
                ->mapWithKeys(function ($items, $userId) {
                    // observación por usuario y fecha.
                    return [$userId => $items->first()->observations];
                });
            // Aquí se inicia el temporaryTimes para cada scheduleId.
            $hours = $this->calculateTotalHours($schedulesOnDate);
            $this->temporaryTimes[$scheduleId] = $hours;
            return [
                'hours' => $this->calculateTotalHours($schedulesOnDate),
                'observations' => $observationsByUser,
                'schedule_id' => $scheduleId, // Asegúrate de agregar esto
            ];
        });

        $this->showPendingApprovalModal = true;
    }

    public function approveWeekSchedules($startDate, $endDate)
    {
        // Convertir las fechas a objetos Carbon
        $start = Carbon::createFromFormat('Y-m-d', $startDate);
        $end = Carbon::createFromFormat('Y-m-d', $endDate);

        // Actualizar los registros en la base de datos
        StudentScheduleModel::whereBetween('start_time', [$start, $end])
            ->where('admin_approval', false)
            ->update(['admin_approval' => true]);

        $this->loadPendingApprovals();

        toastr()->success('Horarios aprobados exitosamente.');
    }

    public function approveDaySchedules($date)
    {
        $schedules = StudentScheduleModel::whereDate('start_time', $date)->get();

        foreach ($schedules as $schedule) {
            // Si student_approval es true y admin_approval es false, o ambos son true, actualiza admin_approval a true
            if ($schedule->student_approval && !$schedule->admin_approval) {
                $schedule->admin_approval = true;
                $schedule->save();
            }
        }

        session()->flash('message', 'Todos los horarios del día ' . $date . ' han sido aprobados.');
        $this->loadPendingApprovals();
    }

    public function updateTotalHours($scheduleId, $newTotalHours)
    {
        $schedule = StudentScheduleModel::find($scheduleId);
        if ($schedule) {
            try {
                // Divide las horas totales en horas, minutos y segundos
                list($hours, $minutes, $seconds) = explode(':', $newTotalHours);

                // Calcula end_time como start_time más la duración proporcionada
                $end_time = Carbon::parse($schedule->start_time)
                    ->addHours($hours)
                    ->addMinutes($minutes)
                    ->addSeconds($seconds);

                // Actualiza end_time solo si la duración es del mismo día
                if ($end_time->isSameDay($schedule->start_time)) {
                    $schedule->end_time = $end_time;
                    $schedule->save();
                    session()->flash('message', 'Horas actualizadas con éxito.');
                } else {
                    session()->flash('error', 'La duración excede el día de inicio.');
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Formato de hora inválido.');
            }
        } else {
            session()->flash('error', 'No se pudo encontrar el horario para actualizar.');
        }
    }

    public function saveTimeOnEnterOrBlur($scheduleId)
    {
        if (array_key_exists($scheduleId, $this->temporaryTimes)) {
            $newTotalHours = $this->temporaryTimes[$scheduleId];
            $this->updateTotalHours($scheduleId, $newTotalHours);
        } else {
            session()->flash('error', 'No se pudo encontrar el horario para actualizar.');
        }
    }

    public function showObservations($date)
    {
        $this->viewingObservationId = $date;
        $this->showObservationsModal = true;
    }

    public function hideObservations()
    {
        $this->viewingObservationId = null;
    }

    public function verifyWeekHours($date)
    {
        try {
            $dateObject = new \DateTime($date);
        } catch (\Exception $e) {
            toastr()->error('Fecha inválida.');
            return;
        }

        $userId = auth()->user()->id;
        $this->allUserSchedules = StudentScheduleModel::where('user_id', $userId)
            ->whereDate('start_time', $dateObject)
            ->get();

        if ($this->allUserSchedules->isNotEmpty()) {
            $this->totalHoursOfDay = $this->calculateTotalHours($this->allUserSchedules);
            $this->showVerificationModal = true;
        } else {
            toastr()->error('No hay registros para verificar en esta fecha.');
        }
    }

    public function loadPendingApprovals()
    {
        //$query = User::query();

        if (!auth()->user()->hasRole('Administrador')) {
            $schedules = StudentScheduleModel::with('user')
                ->join('users', 'student_schedules.user_id', '=', 'users.id')
                ->where('student_schedules.admin_approval', false)
                ->where('student_schedules.student_approval', true)
                ->where('users.current_team_id', auth()->user()->currentTeam->id)
                ->get();

        } else {
            $schedules = StudentScheduleModel::with('user')
                ->where('admin_approval', false)
                ->where('student_approval', true)
                ->get();
        }

        // Agrupa los horarios por semana
        $groupedSchedules = $schedules->groupBy(function ($item) {
            $startOfWeek = Carbon::parse($item->start_time)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            return $startOfWeek->format('Y-m-d') . ' a ' . $endOfWeek->format('Y-m-d');
        });

        // Mapea los resultados para incluir información adicional
        $this->pendingApprovals = $groupedSchedules->map(function ($items, $dateRange) {
            // Agrupa aún más por user_id para calcular las horas por empleado
            $studentsHours = $items->groupBy('user_id')->mapWithKeys(function ($schedules, $userId) {
                $user = $schedules->first()->user;
                $totalHours = $this->calculateTotalHours($schedules);

                return [$userId => ['name' => $user->name, 'totalHours' => $totalHours]];
            });

            list($startDate, $endDate) = explode(' a ', $dateRange);
            return [
                'dateRange' => $dateRange,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'studentsHours' => $studentsHours,
                'schedule_ids' => $items->pluck('id')->all(),
                'schedules' => $items,
            ];
        });
    }

    public function calculateTotalHours($schedules)
    {
        $totalSeconds = 0;
        foreach ($schedules as $schedule) {
            if ($schedule->end_time) {
                $start = Carbon::parse($schedule->start_time);
                $end = Carbon::parse($schedule->end_time);
                $totalSeconds += $end->diffInSeconds($start);
            }
        }

        // Convertir segundos totales a horas, minutos y segundos
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }

    public function approveSchedule($scheduleId)
    {
        $schedule = StudentScheduleModel::find($scheduleId);
        if ($schedule && !$schedule->admin_approval) {
            $schedule->admin_approval = true;
            $schedule->save();
            session()->flash('message', 'Fichaje aprobado con éxito.');
        }

        $this->loadPendingApprovals();
    }

    public function render()
    {
        return view('livewire.schedule-admin', [
            'pendingApprovals' => $this->pendingApprovals,
        ]);
    }
}