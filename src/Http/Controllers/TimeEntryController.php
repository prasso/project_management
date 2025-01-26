<?php

namespace Prasso\ProjectManagement\Http\Controllers;

use Prasso\ProjectManagement\Models\TimeEntry;
use Prasso\ProjectManagement\Models\Task;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    public function index(Request $request)
    {
        $timeEntries = TimeEntry::with(['task.project.client', 'user'])
            ->latest()
            ->get();

        if ($request->wantsJson()) {
            return response()->json($timeEntries->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'start_time' => $entry->start_time,
                    'end_time' => $entry->end_time,
                    'description' => $entry->description,
                    'billable' => $entry->billable,
                    'hourly_rate' => $entry->hourly_rate,
                    'task_id' => $entry->task_id,
                    'task_name' => $entry->task->title,
                    'project_name' => $entry->task->project->name,
                    'client_name' => $entry->task->project->client->name
                ];
            }));
        }

        return view('prasso-pm::time-entries.index', compact('timeEntries'));
    }

    public function create()
    {
        $tasks = Task::pluck('title', 'id');
        return view('prasso-pm::time-entries.create', compact('tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'description' => 'nullable|string',
            'billable' => 'boolean',
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        $validated['user_id'] = auth()->id();

        TimeEntry::create($validated);

        return redirect()->route('prasso-pm.time-entries.index')
            ->with('success', 'Time entry created successfully.');
    }

    public function show(TimeEntry $timeEntry)
    {
        $timeEntry->load(['task.project', 'user']);
        return view('prasso-pm::time-entries.show', compact('timeEntry'));
    }

    public function edit(TimeEntry $timeEntry)
    {
        $tasks = Task::pluck('title', 'id');
        return view('prasso-pm::time-entries.edit', compact('timeEntry', 'tasks'));
    }

    public function update(Request $request, TimeEntry $timeEntry)
    {
        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'description' => 'nullable|string',
        ]);

        $timeEntry->update($validated);

        return response()->json(['message' => 'Time entry updated successfully']);
    }

    public function destroy(TimeEntry $timeEntry)
    {
        $timeEntry->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Time entry deleted successfully']);
        }

        return redirect()->route('prasso-pm.time-entries.index')
            ->with('success', 'Time entry deleted successfully.');
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'description' => 'nullable|string',
            'billable' => 'boolean',
        ]);

        // Stop any currently running time entries
        TimeEntry::where('user_id', auth()->id())
            ->whereNull('end_time')
            ->update(['end_time' => now()]);

        // Create new time entry
        TimeEntry::create([
            'task_id' => $validated['task_id'],
            'user_id' => auth()->id(),
            'start_time' => now(),
            'description' => $validated['description'],
            'billable' => $validated['billable'] ?? true,
        ]);

        return redirect()->back()->with('success', 'Timer started successfully.');
    }

    public function stop(TimeEntry $timeEntry)
    {
        if ($timeEntry->user_id !== auth()->id()) {
            abort(403);
        }

        $timeEntry->update(['end_time' => now()]);

        return redirect()->back()->with('success', 'Timer stopped successfully.');
    }
}
