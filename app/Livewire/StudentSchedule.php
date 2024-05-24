<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\StudentScheduleModel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


                                                          
class StudentSchedule extends Component {
    public $showConfirmationModal = false;
    public $confirmingScheduleId = null;
    public $ultimoRegistroSalidaHora;
    public $showVerificationModal = false;
    public $allUserSchedules;
    public $onwork = false;
    public $dayClosed = false; // Nueva propiedad para controlar el cierre de la jornada
    public $pendingApprovalCount;
    public $showPendingApprovalModal = false;
    public $pendingApprovals;

    public $totalHoursOfDay;
    public $observations; // Agrega la propiedad $observations


    public $totalJornada; 
    public $studentSchedules;
    public $totalRegistrosHoy;
    public $hoursToday;
    public $primerRegistroHoy;
    public $estadoHoy;

    public function mount() {
        $this->loadStudentSchedules();
    }
    
    public function showPendingApprovals()
    {

        $pendingApprovals = collect($this->studentSchedules)
            ->flatMap(function ($dayInfo) {
                return collect($dayInfo['schedules'])->filter(function ($schedule) {
                    return !$schedule['student_approval'];
                });
            })
            ->groupBy(function ($schedule) {
                $date = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $schedule['start_time']);
                return $date ? $date->format('Y-m-d') : null;
            })
            ->map(function ($items, $date) {
                // Obtener el día correspondiente en $this->studentSchedules
                $dayInfo = $this->studentSchedules[$date] ?? null;

                // Verificar si existe información para este día en $this->studentSchedules
                if ($dayInfo) {
                    return [
                        'date' => $date,
                        'count' => $items->count(),
                        'hours' => $dayInfo['hours'],
                        'schedule_ids' => $items->pluck('id')->all(),
                    ];
                } else {
                    // En caso de no encontrar información, retornar valores por defecto o null
                    return [
                        'date' => $date,
                        'count' => $items->count(),
                        'hours' => null,
                        'schedule_ids' => $items->pluck('id')->all(),
                    ];
                }
            });

        $this->pendingApprovals = $pendingApprovals;
        $this->showPendingApprovalModal = true;
    }
    
    public function closePendingApprovalModal()
    {
        $this->showPendingApprovalModal = false;
    }
    

    public function confirmApproveAll() {
        if (!$this->dayClosed) {
            $this->showVerificationModal = false;
            $this->showConfirmationModal = true;
        } else {
            toastr()->error('La jornada ya está cerrada.');
        }
    }
    
    public function loadStudentSchedules() {
        $userId = auth()->user()->id;
        $today = now();
        $startOfWeek = $today->startOfWeek();
    
        $schedules = StudentScheduleModel::where('user_id', $userId)
                                    ->where('start_time', '>=', $startOfWeek)
                                    ->where('student_approval', false)
                                    ->orderBy('start_time')
                                    ->get();
    
        $groupedSchedules = $schedules->groupBy(function ($schedule) {
            return $schedule->start_time->format('Y-m-d');
        });
    
        $this->studentSchedules = $groupedSchedules->map(function ($schedulesOnDate, $date) {
            return [
                'date' => $date,
                'schedules' => $schedulesOnDate,
                'hours' => $this->calculateTotalHours($schedulesOnDate),
                'total' => count($schedulesOnDate),
                'first_start_time' => optional($schedulesOnDate->first())->start_time ? $schedulesOnDate->first()->start_time->format('H:i') : 'N/A',
                'last_end_time' => optional($schedulesOnDate->whereNotNull('end_time')->last())->end_time ? $schedulesOnDate->whereNotNull('end_time')->last()->end_time->format('H:i') : 'N/A',
            ];
        });
    
        // Verificar si el usuario está actualmente trabajando
        $this->onwork = $schedules->whereNull('end_time')
                                  ->where('start_time', '>=', now()->startOfDay())
                                  ->isNotEmpty();
    
        // Actualizar el estado de la jornada
        $this->dayClosed = $this->isJourneyClosedForToday();
    
        // Resto de las actualizaciones
        $this->totalRegistrosHoy = $schedules->where('start_time', '>=', now()->startOfDay())->count();
        $this->hoursToday = $this->calculateTotalHours($schedules->where('start_time', '>=', now()->startOfDay()));
        $this->primerRegistroHoy = optional($schedules->where('start_time', '>=', now()->startOfDay())->first())->start_time;
        $this->estadoHoy = optional($schedules->where('start_time', '>=', now()->startOfDay())->first())->status ?? 'N/A';
        $ultimoRegistro = $schedules->whereNotNull('end_time')->last();
        $this->ultimoRegistroSalidaHora = optional($ultimoRegistro)->end_time ? $ultimoRegistro->end_time->format('H:i:s') : 'N/A';
        $this->totalJornada = $this->calculateTotalHours($schedules);
        $this->pendingApprovalCount = $schedules->where('student_approval', false)->count();
    }
    
    public function toggleWorkday()
    {

        if ($this->isJourneyClosedForToday()) {
            toastr()->error('No puedes iniciar o detener una jornada que ya está cerrada.');
            return;
        }

        if ($this->dayClosed) {
            toastr()->error('No puedes iniciar o detener una jornada que ya está cerrada.');
            return;
        }

        $user_id = auth()->user()->id;
        $now = now();
    
        $isOnWork = StudentScheduleModel::where('user_id', $user_id)
            ->whereDate('start_time', $now->format('Y-m-d'))
            ->whereNull('end_time')
            ->exists();
    
        if ($isOnWork) {
            StudentScheduleModel::where('user_id', $user_id)
                ->whereDate('start_time', $now->format('Y-m-d'))
                ->whereNull('end_time')
                ->update([
                    'end_time' => $now,
                ]);
            toastr()->success('Jornada laboral finalizada.');
        } else {
            StudentScheduleModel::create([
                'user_id' => $user_id,
                'start_time' => $now,
            ]);
            toastr()->success('Jornada laboral iniciada.');
        }
    
        $this->loadStudentSchedules();
    }
    
    public function verifyHours($date) {
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
    

    public function approveSchedule($scheduleId)
    {
        $schedule = StudentScheduleModel::find($scheduleId);
        
        if ($schedule && $schedule->user_id == auth()->user()->id && !$schedule->student_approval) {
            $schedule->update(['student_approval' => true]);
            toastr()->success('Horas aprobadas con éxito.');
        }
        
        $this->loadStudentSchedules();
    }

    public function isJourneyClosedForToday()
    {
        $userId = auth()->user()->id;
        $today = now()->startOfDay();
        $tomorrow = now()->startOfDay()->addDay();

        // Verifica si hay registros aprobados por el empleado para el día actual
        return StudentScheduleModel::where('user_id', $userId)
                            ->where('start_time', '>=', $today)
                            ->where('start_time', '<', $tomorrow)
                            ->where('student_approval', true)
                            ->exists();
    }

        
    public function approveHours() {
        if ($this->confirmingScheduleId) {
            $schedule = StudentScheduleModel::find($this->confirmingScheduleId);
            if ($schedule && $schedule->user_id == auth()->user()->id && !$schedule->student_approval) {
                $schedule->update(['student_approval' => true]);
                toastr()->success('Horas aprobadas con éxito.');
            }
        }
        $this->showConfirmationModal = false;
        $this->confirmingScheduleId = null;
        $this->loadStudentSchedules();
    }

    public function approveAllUserSchedules()
    {
        if ($this->dayClosed) {
            toastr()->error('La jornada de hoy ya está cerrada.');
            return;
        }
    
        $userId = auth()->user()->id;
        $today = now()->format('Y-m-d');
    
        if ($this->isJourneyClosedForToday()) {
        toastr()->error('La jornada de hoy ya está cerrada.');
        return;
        }
        
        // Actualiza todos los registros pendientes
        StudentScheduleModel::where('user_id', $userId)
                        ->update(['student_approval' => true]);

        // Aquí actualizaremos un registro existente o crearemos uno nuevo si no existe
        $record = DB::table('user_daily_records')->where('user_id', $userId)->where('date_of_day', $today)->first();
    
        if ($record) {
            DB::table('user_daily_records')->where('id', $record->id)->update([
                'observations' => $this->observations,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('user_daily_records')->insert([
                'user_id' => $userId,
                'date_of_day' => $today,
                'observations' => $this->observations,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        // Actualiza la propiedad dayClosed (opcionalmente)
        session(["journey_closed_for_{$userId}_today" => $today]);
        $this->dayClosed = true;

        // Deshabilita la opción de iniciar una nueva jornada y hacer registros
        $this->onwork = false;
        $this->showConfirmationModal = false;
        $this->loadStudentSchedules();
    
        toastr()->success('Todos los registros han sido aprobados.');
    }
    
    
    private function calculateTotalHours($schedules) {
        $totalTime = 0;
        foreach ($schedules as $schedule) {
            if (!is_a($schedule, StudentScheduleModel::class)) {
                continue; // Saltar el ciclo si $schedule no es una instancia de StudentScheduleModel
            }
    
            if (!empty($schedule->end_time)) {
                $totalTime += $schedule->end_time->diffInSeconds($schedule->start_time);
            } else {
                $totalTime += now()->diffInSeconds($schedule->start_time);
            }
        }
    
        $hours = floor($totalTime / 3600);
        $minutes = floor(($totalTime / 60) % 60);
        $seconds = $totalTime % 60;
    
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
    
    public function render() {
        $user = auth()->user();
        return view('livewire.student-schedule', compact('user'));
    }
}