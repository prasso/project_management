<?php

namespace Prasso\ProjectManagement\Http\Controllers;

use Prasso\ProjectManagement\Models\Task;
use Prasso\ProjectManagement\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['project', 'assignee'])->latest()->paginate(10);
        return view('prasso-pm::tasks.index', compact('tasks'));
    }

    public function create()
    {
        $projects = Project::pluck('name', 'id');
        return view('prasso-pm::tasks.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:todo,in_progress,review,completed',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|integer|min:0',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Task::create($validated);

        return redirect()->route('prasso-pm.tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $task->load(['project', 'assignedTo', 'timeEntries']);
        return view('prasso-pm::tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $projects = Project::pluck('name', 'id');
        return view('prasso-pm::tasks.edit', compact('task', 'projects'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:todo,in_progress,review,completed',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|integer|min:0',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update($validated);

        return redirect()->route('prasso-pm.tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('prasso-pm.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
