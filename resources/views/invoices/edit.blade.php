@extends('prasso-pm::layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Edit Invoice</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Update invoice information.
                </p>
            </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('prasso-pm.invoices.update', $invoice) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="client_id" class="block text-sm font-medium text-gray-700">Client</label>
                                <select id="client_id" name="client_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select a client</option>
                                    @foreach($clients as $id => $name)
                                        <option value="{{ $id }}" {{ $invoice->client_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="issue_date" class="block text-sm font-medium text-gray-700">Issue Date</label>
                                <input type="date" name="issue_date" id="issue_date" value="{{ $invoice->issue_date->format('Y-m-d') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" @if($invoice->status === 'paid') disabled @endif>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                                <input type="date" name="due_date" id="due_date" value="{{ $invoice->due_date->format('Y-m-d') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" @if($invoice->status === 'paid') disabled @endif>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="tax_rate" class="block text-sm font-medium text-gray-700">Tax Rate (%)</label>
                                <input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="100" value="{{ $invoice->tax_rate }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" @if($invoice->status === 'paid') disabled @endif>
                                    <option value="pending" {{ $invoice->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="sent" {{ $invoice->status === 'sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>

                            <div class="col-span-6">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ $invoice->notes }}</textarea>
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                                <input type="text" name="amount" id="amount" value="{{ $invoice->total }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" readonly>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">Invoice Items</h4>
                                <div id="invoice-items" class="space-y-4 mt-4">
                                    @foreach($invoice->items as $index => $item)
                                    <div class="invoice-item border rounded-md p-4">
                                        <div class="grid grid-cols-6 gap-4">
                                            <div class="col-span-6 sm:col-span-3">
                                                <label class="block text-sm font-medium text-gray-700">Project</label>
                                                <select name="items[{{ $index }}][project_id]" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="">Select a project</option>
                                                    @foreach($projects as $id => $name)
                                                        <option value="{{ $id }}" {{ $item->project_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                                <input type="text" name="items[{{ $index }}][description]" value="{{ $item->description }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required min="0">
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                <label class="block text-sm font-medium text-gray-700">Rate</label>
                                                <input type="number" name="items[{{ $index }}][rate]" value="{{ $item->rate }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required min="0">
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @unless($invoice->status === 'paid')
                                <button type="button" id="add-item" class="mt-4 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Add Item
                                </button>
                                @endunless
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Invoice
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let itemCount = {{ count($invoice->items) }};
    const addItemButton = document.getElementById('add-item');
    if (addItemButton) {   
    document.getElementById('add-item').addEventListener('click', function() {
        const template = document.querySelector('.invoice-item').cloneNode(true);
        const inputs = template.querySelectorAll('input, select');
        
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            input.setAttribute('name', name.replace(/\[\d+\]/, `[${itemCount}]`));
            input.value = '';
        });
        
        document.getElementById('invoice-items').appendChild(template);
        itemCount++;
    });
}
</script>
@endpush
@endsection
