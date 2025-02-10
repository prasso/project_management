@extends('prasso-pm::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Task Details</h1>
        <div class="space-x-2">
            <a href="{{ route('prasso-pm.tasks.edit', $task) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Edit Task</a>
            <a href="{{ route('prasso-pm.tasks.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Back to Tasks</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold mb-4">Task Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Project</label>
                        <p>{{ $task->project->name }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Title</label>
                        <p>{{ $task->name }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Description</label>
                        <p class="whitespace-pre-wrap">{{ $task->description }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Status</label>
                        <span class="px-2 py-1 rounded text-sm 
                            @if($task->status === 'completed') bg-green-100 text-green-800
                            @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                            @elseif($task->status === 'review') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4">Additional Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Priority</label>
                        <span class="px-2 py-1 rounded text-sm
                            @if($task->priority === 'high') bg-red-100 text-red-800
                            @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Due Date</label>
                        <p>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'Not set' }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Estimated Hours</label>
                        <p>{{ $task->estimated_hours ?? 'Not set' }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Assigned To</label>
                        <p>{{ $task->assignee ? $task->assignee->name : 'Unassigned' }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($task->timeEntries && $task->timeEntries->count() > 0)
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-4">Time Entries</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($task->timeEntries as $entry)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $entry->date ? $entry->date->format('M d, Y') : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $entry->hours }}</td>
                            <td class="px-6 py-4 text-sm">{{ $entry->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $entry->user->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
