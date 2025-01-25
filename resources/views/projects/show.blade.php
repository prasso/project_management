@extends('prasso-pm::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $project->name }}</h1>
        <div class="space-x-2">
            <a href="{{ route('prasso-pm.projects.edit', $project) }}" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                Edit Project
            </a>
            <a href="{{ route('prasso-pm.projects.index') }}" 
                class="text-gray-600 hover:text-gray-900">
                Back to Projects
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Project Details -->
        <div class="md:col-span-2">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Project Details</h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Client</h3>
                        <p class="mt-1">{{ $project->client ? $project->client->name : 'No Client' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Description</h3>
                        <p class="mt-1 whitespace-pre-line">{{ $project->description ?: 'No description provided' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Start Date</h3>
                            <p class="mt-1">{{ $project->start_date ? $project->start_date->format('M d, Y') : 'Not set' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Due Date</h3>
                            <p class="mt-1">{{ $project->due_date ? $project->due_date->format('M d, Y') : 'Not set' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Budget</h3>
                            <p class="mt-1">{{ $project->budget ? '$'.number_format($project->budget, 2) : 'Not set' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Hourly Rate</h3>
                            <p class="mt-1">{{ $project->hourly_rate ? '$'.number_format($project->hourly_rate, 2) : 'Not set' }}</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Status</h3>
                        <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @switch($project->status)
                                @case('completed')
                                    bg-green-100 text-green-800
                                    @break
                                @case('in_progress')
                                    bg-blue-100 text-blue-800
                                    @break
                                @case('on_hold')
                                    bg-yellow-100 text-yellow-800
                                    @break
                                @case('cancelled')
                                    bg-red-100 text-red-800
                                    @break
                                @default
                                    bg-gray-100 text-gray-800
                            @endswitch
                        ">
                            {{ str_replace('_', ' ', ucfirst($project->status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Summary -->
        <div>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Tasks</h2>
                    <a href="{{ route('prasso-pm.tasks.create', ['project_id' => $project->id]) }}" 
                        class="text-sm text-blue-500 hover:text-blue-600">
                        Add Task
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($project->tasks as $task)
                        <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('prasso-pm.tasks.show', $task) }}" 
                                    class="text-blue-600 hover:text-blue-900">
                                    {{ $task->name }}
                                </a>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @switch($task->status)
                                        @case('completed')
                                            bg-green-100 text-green-800
                                            @break
                                        @case('in_progress')
                                            bg-blue-100 text-blue-800
                                            @break
                                        @default
                                            bg-gray-100 text-gray-800
                                    @endswitch
                                ">
                                    {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                </span>
                            </div>
                            @if($task->due_date)
                                <p class="text-sm text-gray-500 mt-1">
                                    Due: {{ $task->due_date->format('M d, Y') }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No tasks created yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
