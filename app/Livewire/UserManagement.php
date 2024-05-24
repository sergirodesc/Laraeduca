<?php

namespace App\Livewire;

use App\Jobs\ProcessUploadDocument;
use App\Models\Document;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserManagement extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Public properties
    public $status;
    public $team;
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $search = '';
    public $teams = [];
    public $managingFiles = false;
    public $currentFolder = 'Public';
    public $publicFiles = [], $privateFiles = [];
    public $isModalOpen = false;
    public $isTeamModalOpen = false;
    public $newUser = [
        'name' => '',
        'email' => '',
        'password' => '',
    ];
    public $selectedUser;
    public $selectedUserID;
    public $selectedTeam;
    public $selectedSpatieRole;
    public $rules = [
        'selectedUser.status' => 'required|in:0,1',
        'selectedSpatieRole' => 'required',
        'selectedUser.name' => 'required',
        'selectedUser.email' => 'required',
    ];
    public $perPage = 10;
    public $perPageDocuments = 5;
    public $documentType;
    public $expiryDate;
    public $file;
    public $input_file;

    public function mount()
    {
        $this->loadTeams();
    }

    // Load teams from the database
    public function loadTeams()
    {
        $teams = Team::all();

        foreach ($teams as $team) {
            $this->teams[$team->id] = $team->name;
        }
    }

    // Assign team to a user
    public function assignTeam($userId)
    {
        $user = User::find($userId);
        $this->selectedUser = $user->toArray(); 
        if ($user->teams->isNotEmpty()) {
            $this->selectedTeam = $user->teams->first()->id;
        } else {
            $this->selectedTeam = null;
        }

        $this->selectedSpatieRole = $user->roles->isNotEmpty() ? $user->roles->first()->name : null;
        $this->isTeamModalOpen = true;
    }

    // Change user status
    public function changeUserStatus($userId, $currentStatus)
    {
        $user = User::find($userId);

        if ($user) {
            $user->forceFill([
                'status' => $currentStatus === 1 ? 0 : 1,
            ])->save();
        }
    }

    // Sort users by field
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    // Open user creation modal
    public function openModal()
    {
        $this->isModalOpen = true;
    }

    // Close user creation modal
    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    // Create a new user
    public function createUser()
    {
        // Validate user input
        $validatedData = $this->validate([
            'newUser.name' => 'required|string|max:255',
            'newUser.email' => 'required|email|unique:users,email',
            'newUser.password' => 'required|string|min:8',
        ], [
            'newUser.name.required' => 'El nombre es obligatorio.',
            'newUser.email.required' => 'El email es obligatorio.',
            'newUser.email.email' => 'Debe ser un email válido.',
            'newUser.email.unique' => 'Este email ya está en uso.',
            'newUser.password.required' => 'La contraseña es obligatoria.',
            'newUser.password.min' => 'La contraseña debe tener al menos :min caracteres.',
        ]);

        User::create([
            'name' => $this->newUser['name'],
            'email' => $this->newUser['email'],
            'password' => bcrypt($this->newUser['password']),
        ]);

        $this->newUser = [
            'name' => '',
            'email' => '',
            'password' => '',
        ];

        $this->closeModal();
    }

    // Save team assignment
    public function saveTeam()
    {
        
        $this->validate([
            'selectedUser.status' => 'required|in:0,1',
        ]);
        $user = User::find($this->selectedUser['id']);
        //dd($user);
        $team = $this->selectedTeam ? Team::find($this->selectedTeam) : null;
        $user->status = $this->selectedUser['status'];
        $user->name = $this->selectedUser['name'];
        $user->email = $this->selectedUser['email'];
        
        if ($team) {
            $user->current_team_id = $team->id;
        }
        
        if ($user) {
            
            if ($this->selectedSpatieRole) {
                $spatieRole = Role::where('name', $this->selectedSpatieRole)->first();
                $user->syncRoles([$spatieRole->id]);
            }
            
            if ($team) {
                $role = 'student';
                $user->save();
                $this->closeTeamModal();
                app(\App\Actions\Jetstream\AddTeamMember::class)->add(
                    auth()->user(),
                    $team,
                    $user->email,
                    $role,
                );
            }

            $user->save();

            $this->closeTeamModal();
            $this->dispatch('teamAssigned');
        }
    }

    // Assign Spatie role to user
    public function assignSpatieRoleToUser($userId, $spatieRoleName)
    {
        $user = User::findOrFail($userId);
        $spatieRole = Role::where('name', $spatieRoleName)->firstOrFail();

        $user->assignRole($spatieRole);

        return response()->json(['message' => 'Rol asignado correctamente']);
    }

    public function closeTeamModal()
    {
        $this->isTeamModalOpen = false;
        //$this->selectedUser = null;
        $this->selectedTeam = null;
    }

    // Reset pagination when search query is updated
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Reset pagination when per page value is updated
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    
    // Load files for user
    public function updatedManagingFiles($userId)
    {
        $this->resetFileManagement();
        if ($userId) {
            $this->loadFilesForUser($userId);
        }
    }

    private function resetFileManagement()
    {
        $this->publicFiles = [];
        $this->privateFiles = [];
        $this->input_file = [];
        $this->currentFolder = 'Public';
        $this->resetPage('documentsPage');
    }

    // Set current folder for file management
    public function setCurrentFolder($folder)
    {
        $this->currentFolder = $folder;
    }

    // Load files for a specific user
    public function loadFilesForUser($userId)
    {
        $user = User::find($userId);

        $directory = "Employees/{$user->id}";

        if (Storage::exists($directory)) {
            $this->publicFiles = Storage::files($directory);
        } else {
            $this->publicFiles = [];
        }
    }

    // Delete a file
    public function deleteFile($filename)
    {
        $document = Document::where('file_path', $filename)->first();

        if ($document) {
            if (Storage::exists($filename)) {
                Storage::delete($filename);
            }

            $document->delete();

            toastr()->success('Archivo eliminado con éxito.');

            $this->loadFilesForUser($this->managingFiles);
        } else {
            toastr()->error('El archivo no existe en la base de datos.');
        }
    }

    // Save uploaded file
    public function saveFile()
    {
        // Validate file upload
        $this->validate([
            'documentType' => 'required|string',
            'expiryDate' => 'required|date',
            'file' => 'required|file',
        ]);

        $fileName = $this->file->getClientOriginalName();
        $path = "Employees/{$this->managingFiles}";

        $filePath = $this->file->storeAs($path, $fileName);

        $document = new Document();
        $document->document_type = $this->documentType;
        $document->expiry_date = $this->expiryDate;
        $document->file_path = $filePath;
        $document->user_id = $this->managingFiles;
        $document->save();

        $this->reset(['documentType', 'expiryDate', 'file']);
        $this->managingFiles = false;

        toastr()->success('El documento se ha cargado correctamente.');
    }

    // Download file
    public function download($filePath)
    {
        try {
            if (!Storage::exists($filePath)) {
                return;
            }

            $fileContent = Storage::get($filePath);
            $fileName = basename($filePath);

            return response()->streamDownload(function () use ($fileContent) {
                echo $fileContent;
            }, $fileName);

        } catch (\Exception $e) {

        }
    }

    // Unset file from input files array
    public function unsetFromInputFiles($index)
    {
        if (isset($this->input_file[$index])) {
            unset($this->input_file[$index]);
        }
    }

    // Close file manager
    public function closeManager()
    {
        $this->resetFileManagement();
        $this->managingFiles = false;
        $this->resetPage('documentsPage');
    }

    // Get formatted file size
    public function getFileSize($filePath)
    {
        if (Storage::exists($filePath)) {
            $size = Storage::size($filePath);
            return $this->formatBytes($size);
        }

        return 'N/A';
    }

    // Format bytes to human-readable format
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    // Render the Livewire component
    public function render()
    {
        $query = User::query();

        if ($this->status !== null && $this->status !== '') {
            $query->where('status', $this->status);
        }

        if (!empty($this->team)) {
            $query->whereHas('teams', function ($query) {
                $query->where('name', $this->team);
            });
        }

        $users = $query->where(function ($query) {
            $query->where('name', 'LIKE', '%' . $this->search . '%')
                ->orWhere('email', 'LIKE', '%' . $this->search . '%');
        })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $spatieRoles = Role::all();

        $documentsQuery = Document::query();

        $documents = $documentsQuery->where('user_id', $this->managingFiles)->paginate($this->perPageDocuments, ['*'], 'documentsPage');

        return view('livewire.user-management', compact('users', 'spatieRoles', 'documents'));
    }
}