<?php

namespace Prasso\ProjectManagement\Http\Controllers;

use Prasso\ProjectManagement\Models\Task;
use Prasso\ProjectManagement\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['project.client', 'assignee'])->latest()->paginate(10);
        
        if (request()->wantsJson()) {
            return response()->json($tasks);
        }

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
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed,on_hold',
            'assigned_to' => 'nullable|exists:users,id',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        $task = Task::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Task created successfully', 'task' => $task]);
        }

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
            'name' => 'required|string|max:255',
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

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Task deleted successfully']);
        }

        return redirect()->route('prasso-pm.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
