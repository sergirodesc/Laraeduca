<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Team;

class TeamsList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Team::query();

        $teams = $query->where('name', 'LIKE', '%' . $this->search . '%')
                      ->orderBy($this->sortField, $this->sortDirection)
                      ->paginate(10);

        return view('livewire.teams-list', [
            'teams' => $teams,
        ]);
    }
}