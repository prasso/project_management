@extends('prasso-pm::layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Create New Invoice</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Generate a new invoice for your client.
                </p>
            </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('prasso-pm.invoices.store') }}" method="POST">
                @csrf
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="client_id" class="block text-sm font-medium text-gray-700">Client</label>
                                <select id="client_id" name="client_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select a client</option>
                                    @foreach($clients as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Client Information Section -->
                            <div id="client-info" class="col-span-6 hidden">
                                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                    <h4 class="text-lg font-medium text-gray-900 mb-2">Client Information</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600">Company</p>
                                            <p id="client-company" class="font-medium"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Email</p>
                                            <p id="client-email" class="font-medium"></p>
                                        </div>
                                        <div class="col-span-2">
                                            <p class="text-sm text-gray-600">Address</p>
                                            <p id="client-address" class="font-medium"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="issue_date" class="block text-sm font-medium text-gray-700">Issue Date</label>
                                <input type="date" name="issue_date" id="issue_date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="tax_rate" class="block text-sm font-medium text-gray-700">Tax Rate (%)</label>
                                <input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="100" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea id="notes" name="notes" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">Time Entries</h4>
                                <div id="invoice-table" class="space-y-4 mt-4">
                                    <div class="invoice-item border rounded-md p-4">
                                        <div id="invoice-items" class="overflow-x-auto">
                                        </div>
                                    </div>
                                </div>
                                <div id="invoice-totals" class="mt-4 p-4 bg-gray-50 rounded-lg hidden">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Total Hours:</p>
                                            <p id="total-hours" class="text-lg font-bold"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Total Amount:</p>
                                            <p id="total-amount" class="text-lg font-bold"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create Invoice
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let itemCount = 1;
    let timeEntries = [];
    
    document.getElementById('client_id').addEventListener('change', function() {
        var clientId = this.value;
        if (clientId) {
            fetch(`/prasso-pm/invoices/uninvoiced-time-entries/${clientId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                // Show client information
                document.getElementById('client-info').classList.remove('hidden');
                document.getElementById('client-company').textContent = data.client.company || 'N/A';
                document.getElementById('client-email').textContent = data.client.email || 'N/A';
                document.getElementById('client-address').textContent = [
                    data.client.address,
                    data.client.city,
                    data.client.state,
                    data.client.zip,
                    data.client.country
                ].filter(Boolean).join(', ');

                timeEntries = data.timeEntries;
                let totalHours = 0;
                let totalAmount = 0;

                // Create table with time entries
                let tableContent = `
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration (hours)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                `;

                data.timeEntries.forEach((entry, index) => {
                    const startTime = new Date(entry.start_time);
                    const endTime = new Date(entry.end_time);
                    const duration = (endTime - startTime) / (1000 * 60 * 60); // duration in hours
                    const amount = duration * (entry.hourly_rate || 0);
                    
                    totalHours += duration;
                    totalAmount += amount;

                    tableContent += `
                        <tr>
                            <td class="px-6 py-4 whitespace-normal text-sm text-gray-900">${entry.description || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${startTime.toLocaleString()}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${endTime.toLocaleString()}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${duration.toFixed(2)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$${entry.hourly_rate || 0}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$${amount.toFixed(2)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <input type="checkbox" name="selected_entries[]" value="${entry.id}" 
                                    class="entry-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    data-task-id="${entry.task_id}"
                                    data-project-id="${entry.task?.project_id}"
                                    data-description="${entry.description || ''}"
                                    data-duration="${duration.toFixed(2)}"
                                    data-rate="${entry.hourly_rate || 0}">
                                <!-- Hidden fields for invoice items -->
                                <input type="hidden" name="items[${index}][project_id]" value="${entry.task?.project_id || ''}" disabled>
                                <input type="hidden" name="items[${index}][task_id]" value="${entry.task_id}" disabled>
                                <input type="hidden" name="items[${index}][description]" value="${entry.description || ''}" disabled>
                                <input type="hidden" name="items[${index}][quantity]" value="${duration.toFixed(2)}" disabled>
                                <input type="hidden" name="items[${index}][rate]" value="${entry.hourly_rate || 0}" disabled>
                            </td>
                        </tr>
                    `;
                });

                tableContent += '</tbody></table>';
                document.getElementById('invoice-items').innerHTML = tableContent;
                
                // Show and update totals
                document.getElementById('invoice-totals').classList.remove('hidden');
                document.getElementById('total-hours').textContent = totalHours.toFixed(2) + ' hours';
                document.getElementById('total-amount').textContent = '$' + totalAmount.toFixed(2);

                // Add select all functionality
                document.getElementById('select-all').addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.entry-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                        // Enable/disable the associated hidden fields
                        const row = checkbox.closest('tr');
                        const hiddenFields = row.querySelectorAll('input[type="hidden"]');
                        hiddenFields.forEach(field => {
                            field.disabled = !this.checked;
                        });
                    });
                });

                // Add individual checkbox functionality
                document.querySelectorAll('.entry-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const row = this.closest('tr');
                        const hiddenFields = row.querySelectorAll('input[type="hidden"]');
                        hiddenFields.forEach(field => {
                            field.disabled = !this.checked;
                        });
                    });
                });
            });
        } else {
            document.getElementById('client-info').classList.add('hidden');
            document.getElementById('invoice-items').innerHTML = '';
            document.getElementById('invoice-totals').classList.add('hidden');
        }
    });
</script>
@endpush
@endsection
