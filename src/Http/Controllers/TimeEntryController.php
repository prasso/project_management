<?php

namespace Prasso\ProjectManagement\Http\Controllers;

use Prasso\ProjectManagement\Models\TimeEntry;
use Prasso\ProjectManagement\Models\Task;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    public function index()
    {
        $timeEntries = TimeEntry::with(['task.project', 'user'])
            ->latest()
            ->paginate(10);
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
            'task_id' => 'required|exists:tasks,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'description' => 'nullable|string',
            'billable' => 'boolean',
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        $timeEntry->update($validated);

        return redirect()->route('prasso-pm.time-entries.index')
            ->with('success', 'Time entry updated successfully.');
    }

    public function destroy(TimeEntry $timeEntry)
    {
        $timeEntry->delete();

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
