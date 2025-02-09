@extends('prasso-pm::layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Invoice #{{ $invoice->invoice_number }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Created on {{ $invoice->created_at->format('M d, Y') }}
                </p>
            </div>
            <div class="flex space-x-3">
                @if($invoice->status === 'draft')
                    <a href="{{ route('prasso-pm.invoices.edit', $invoice) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Edit Invoice
                    </a>
                    <form action="{{ route('prasso-pm.invoices.mark-as-sent', $invoice) }}" method="POST" class="inline">
                        @csrf
                        @method('POST')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Mark as Sent
                        </button>
                    </form>
                @elseif($invoice->status === 'sent')
                    <form action="{{ route('prasso-pm.invoices.mark-as-paid', $invoice) }}" method="POST" class="inline">
                        @csrf
                        @method('POST')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Mark as Paid
                        </button>
                    </form>
                @endif

                @unless(in_array($invoice->status, ['paid', 'cancelled']))
                    <form action="{{ route('prasso-pm.invoices.cancel', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this invoice?');">
                        @csrf
                        @method('POST')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Cancel Invoice
                        </button>
                    </form>
                @endunless

                <button onclick="printInvoice()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Print Invoice
                </button>
            </div>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Client</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $invoice->client->name }} / {{ $invoice->client->company }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Issue Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $invoice->issue_date->format('M d, Y') }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $invoice->due_date->format('M d, Y') }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @switch($invoice->status)
                            @case('paid')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>
                                @break
                            @case('sent')
                                @if($invoice->due_date < now())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Overdue</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Sent</span>
                                @endif
                                @break
                            @case('draft')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Draft</span>
                                @break
                            @case('cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Cancelled</span>
                                @break
                            @default
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                        @endswitch
                    </dd>
                </div>
                @if($invoice->notes)
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $invoice->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Invoice Items
            </h3>
        </div>
        <div class="border-t border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->project->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item->quantity, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${{ number_format($item->rate, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${{ number_format($item->quantity * $item->rate, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-500">Subtotal</td>
                        <td class="px-6 py-3 text-right text-sm text-gray-900">${{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    @if($invoice->tax_rate > 0)
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-500">Tax ({{ number_format($invoice->tax_rate, 2) }}%)</td>
                        <td class="px-6 py-3 text-right text-sm text-gray-900">${{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="bg-gray-100">
                        <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total</td>
                        <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">${{ number_format($invoice->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function printInvoice() {
        const invoiceData = {
            invoiceNumber: '{{ $invoice->invoice_number }}',
            date: '{{ $invoice->issue_date->format('Y-m-d') }}',
            dueDate: '{{ $invoice->due_date->format('Y-m-d') }}',
            client: {
                name: '{{ $invoice->client->name }}',
                company: '{{ $invoice->client->company }}',
                email: '{{ $invoice->client->email }}',
                address: '{{ $invoice->client->address }}',
                city: '{{ $invoice->client->city }}',
                state: '{{ $invoice->client->state }}',
                zip: '{{ $invoice->client->zip }}',
                phone: '{{ $invoice->client->phone }}'
            },
            items: {!! $invoice->items->map(function($item) {
                return [
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'rate' => $item->rate,
                    'amount' => $item->amount,
                    'project' => $item->project ? $item->project->name : null,
                    'task' => $item->task ? $item->task->name : null
                ];
            })->toJson() !!},
            subtotal: {{ $invoice->subtotal }},
            taxRate: {{ $invoice->tax_rate }},
            taxAmount: {{ $invoice->tax_amount }},
            total: {{ $invoice->total }}
        };

        const printWindow = window.open('', '_blank');
        displayInvoicePreview(invoiceData);

        // After printing, mark the invoice as pending
        fetch('{{ route('prasso-pm.invoices.mark-as-pending', $invoice) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show updated status
                window.location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function displayInvoicePreview(data) {
        const items = data.items.map(item => ({
            description: item.description,
            quantity: Number(item.quantity),
            rate: Number(item.rate),
            amount: Number(item.amount),
            project: item.project || '',
            task: item.task || ''
        }));

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>${data.client.company} - Invoice ${data.date} #${data.invoiceNumber}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { margin-bottom: 30px; }
                    .header h1 { margin-bottom: 10px; }
                    .header p { margin: 0; line-height: 1.2; }
                    .client-info { margin-bottom: 20px; }
                    .client-info h3 { margin-bottom: 5px; }
                    .client-info p { margin: 0; line-height: 1.2; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
                    .amounts { text-align: right; margin-top: 20px; }
                    .amounts p { margin: 5px 0; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>${data.client.company}</h1>
                    <p>Invoice Date: ${data.date}</p>
                    <p>Invoice #${data.invoiceNumber}</p>
                    <p>Due Date: ${data.dueDate}</p>
                </div>
                <div class="client-info">
                    <h3>Bill To:</h3>
                    <p>${data.client.name} / ${data.client.company}</p>
                    <p>${data.client.address || ''}</p>
                    <p>${data.client.city || ''}, ${data.client.state || ''} ${data.client.zip || ''}</p>
    
                    <p>${data.client.email}</p>
                    <p>${data.client.phone || ''}</p>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Project</th>
                            <th>Task</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map(item => `
                            <tr>
                                <td>${item.description}</td>
                                <td>${item.project}</td>
                                <td>${item.task}</td>
                                <td>${item.quantity}</td>
                                <td>$${item.rate.toFixed(2)}</td>
                                <td>$${item.amount.toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                <div class="amounts">
                    <p>Subtotal: $${Number(data.subtotal).toFixed(2)}</p>
                    ${Number(data.taxRate) > 0 ? `
                        <p>Tax Rate: ${Number(data.taxRate).toFixed(2)}%</p>
                        <p>Tax Amount: $${Number(data.taxAmount).toFixed(2)}</p>
                    ` : ''}
                    <p><strong>Total: $${Number(data.total).toFixed(2)}</strong></p>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
</script>
@endpush

@endsection
