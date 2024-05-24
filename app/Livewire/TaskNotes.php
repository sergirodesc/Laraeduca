<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Note;

class TaskNotes extends Component
{
    public Task $task;
    public $nuevaNota;

    protected $rules = [
        'nuevaNota' => 'required|string',
    ];

    public function mount(Task $task)
    {
        $this->task = $task;
    }

    public function render()
    {
        return view('livewire.task-manager');
    }

    public function agregarNota()
    {
        $this->validate();

        $this->task->notes()->create([
            'content' => $this->nuevaNota,
        ]);

        $this->nuevaNota = '';

        $this->task = $this->task->fresh(); // Actualiza la tarea para mostrar las notas actualizadas
    }
}
