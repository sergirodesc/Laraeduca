<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskDetailController extends Controller
{
    public function __invoke(Task $task)
    {
        return view('tasks.show', compact('task'));
    }
}
