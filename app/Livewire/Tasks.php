<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Team;

class Tasks extends Component
{
    public $showingTaskModal = false;
    public $taskName;
    public $description;
    public $selectedTeam;
    public $taskNote;

    protected $rules = [
        'taskName' => 'required|string|max:255',
        'description' => 'required|string|max:255',
        'selectedTeam' => 'required|exists:teams,id',
    ];

    public function render()
    {
        $teams = Team::orderBy('name')->get();
        $tasks = Task::all();

        return view('livewire.tasks', compact('teams', 'tasks'));
    }

    public function saveTask()
    {
        $this->validate();

        Task::create([
            'name' => $this->taskName,
            'description' => $this->description,
            'team_id' => $this->selectedTeam,
        ]);

        $this->reset(['taskName', 'selectedTeam', 'showingTaskModal']);
        $this->dispatch('tareaAgregada');
    }

    public function saveGrade($taskId)
    {
        $task = Task::findOrFail($taskId);
        dd($this->taskNote);
        $task->update([
            'grade' => $this->taskNote ?? 1,
        ]);

        unset($this->taskNote);
    }
}
