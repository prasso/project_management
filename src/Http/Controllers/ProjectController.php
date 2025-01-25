<?php

namespace Prasso\ProjectManagement\Http\Controllers;

use Prasso\ProjectManagement\Models\Project;
use Prasso\ProjectManagement\Models\Client;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('client')->latest()->paginate(10);
        return view('prasso-pm::projects.index', compact('projects'));
    }

    public function create()
    {
        $clients = Client::pluck('name', 'id');
        return view('prasso-pm::projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pending,in_progress,completed,on_hold,cancelled',
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        Project::create($validated);

        return redirect()->route('prasso-pm.projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['client', 'tasks']);
        return view('prasso-pm::projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $clients = Client::pluck('name', 'id');
        return view('prasso-pm::projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pending,in_progress,completed,on_hold,cancelled',
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        $project->update($validated);

        return redirect()->route('prasso-pm.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('prasso-pm.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
