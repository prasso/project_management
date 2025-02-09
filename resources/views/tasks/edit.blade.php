@extends('prasso-pm::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit Task</h1>
        <a href="{{ route('prasso-pm.tasks.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Back to Tasks</a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('prasso-pm.tasks.update', $task) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="project_id" class="block text-gray-700 font-bold mb-2">Project</label>
                <select name="project_id" id="project_id" class="form-select w-full @error('project_id') border-red-500 @enderror" required>
                    <option value="">Select Project</option>
                    @foreach($projects as $id => $name)
                        <option value="{{ $id }}" {{ (old('project_id') ?? $task->project_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('project_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Title</label>
                <input type="text" name="name" id="name" class="form-input w-full @error('name') border-red-500 @enderror" value="{{ old('name', $task->name) }}" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
                <textarea name="description" id="description" rows="4" class="form-textarea w-full @error('description') border-red-500 @enderror">{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="priority" class="block text-gray-700 font-bold mb-2">Priority</label>
                    <select name="priority" id="priority" class="form-select w-full @error('priority') border-red-500 @enderror" required>
                        <option value="low" {{ (old('priority', $task->priority) == 'low') ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ (old('priority', $task->priority) == 'medium') ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ (old('priority', $task->priority) == 'high') ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ (old('priority', $task->priority) == 'urgent') ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-gray-700 font-bold mb-2">Status</label>
                    <select name="status" id="status" class="form-select w-full @error('status') border-red-500 @enderror" required>
                        <option value="todo" {{ (old('status', $task->status) == 'todo') ? 'selected' : '' }}>To Do</option>
                        <option value="in_progress" {{ (old('status', $task->status) == 'in_progress') ? 'selected' : '' }}>In Progress</option>
                        <option value="review" {{ (old('status', $task->status) == 'review') ? 'selected' : '' }}>Review</option>
                        <option value="completed" {{ (old('status', $task->status) == 'completed') ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="due_date" class="block text-gray-700 font-bold mb-2">Due Date</label>
                    <input type="date" name="due_date" id="due_date" class="form-input w-full @error('due_date') border-red-500 @enderror" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                    @error('due_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="estimated_hours" class="block text-gray-700 font-bold mb-2">Estimated Hours</label>
                    <input type="number" name="estimated_hours" id="estimated_hours" class="form-input w-full @error('estimated_hours') border-red-500 @enderror" value="{{ old('estimated_hours', $task->estimated_hours) }}" min="0" step="0.5">
                    @error('estimated_hours')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Update Task</button>
            </div>
        </form>
    </div>
</div>
@endsection
