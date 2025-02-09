<?php

namespace Prasso\ProjectManagement\Http\Controllers;

use Prasso\ProjectManagement\Models\Invoice;
use Prasso\ProjectManagement\Models\Client;
use Prasso\ProjectManagement\Models\Project;
use Illuminate\Http\Request;
use DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['client', 'items'])->latest()->paginate(10);
        
        if (request()->wantsJson()) {
            return response()->json($invoices);
        }

        return view('prasso-pm::invoices.index', compact('invoices'));
    }

    public function create()
    {
        $clients = Client::pluck('name', 'id');
        $projects = Project::pluck('name', 'id');
       
        return view('prasso-pm::invoices.create', compact('clients', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.project_id' => 'nullable|exists:projects,id',
            'items.*.task_id' => 'nullable|exists:tasks,id',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        // Set default 'N/A' for null descriptions
        foreach ($validated['items'] as $key => $item) {
            $validated['items'][$key]['description'] = $item['description'] ?? 'N/A';
        }

        $invoice = Invoice::create([
            'client_id' => $validated['client_id'],
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'tax_rate' => $validated['tax_rate'] ?? 0,
            'notes' => $validated['notes'],
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create($item);
        }

        $invoice->calculateTotals()->save();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Invoice created successfully', 'invoice' => $invoice]);
        }

        return redirect()->route('prasso-pm.invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'items.project', 'items.task']);
        return view('prasso-pm::invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $clients = Client::pluck('name', 'id');
        $projects = Project::pluck('name', 'id');
        return view('prasso-pm::invoices.edit', compact('invoice', 'clients', 'projects'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Only draft invoices can be edited.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|exists:invoice_items,id',
            'items.*.project_id' => 'nullable|exists:projects,id',
            'items.*.task_id' => 'nullable|exists:tasks,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($invoice, $validated) {
            $invoice->update([
                'client_id' => $validated['client_id'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'notes' => $validated['notes'],
            ]);

            // Handle invoice items
            if (isset($validated['items'])) {
                // Delete removed items
                $itemIds = collect($validated['items'])->pluck('id')->filter()->toArray();
                $invoice->items()->whereNotIn('id', $itemIds)->delete();

                // Update or create items
                foreach ($validated['items'] as $item) {
                    if (isset($item['id'])) {
                        $invoice->items()->where('id', $item['id'])->update([
                            'project_id' => $item['project_id'],
                            'task_id' => $item['task_id'],
                            'description' => $item['description'],
                            'quantity' => $item['quantity'],
                            'rate' => $item['rate'],
                            'amount' => $item['quantity'] * $item['rate'],
                        ]);
                    } else {
                        $invoice->items()->create([
                            'project_id' => $item['project_id'],
                            'task_id' => $item['task_id'],
                            'description' => $item['description'],
                            'quantity' => $item['quantity'],
                            'rate' => $item['rate'],
                            'amount' => $item['quantity'] * $item['rate'],
                        ]);
                    }
                }
            }

            $invoice->calculateTotals();
            $invoice->save();
        });

        return redirect()->route('prasso-pm.invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Invoice deleted successfully']);
        }

        return redirect()->route('prasso-pm.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function markAsPaid(Invoice $invoice)
    {
        if ($invoice->status !== 'sent') {
            return back()->with('error', 'Only sent invoices can be marked as paid.');
        }

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        return back()->with('success', 'Invoice has been marked as paid.');
    }

    public function markAsSent(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Only draft invoices can be marked as sent.');
        }

        $invoice->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);

        return back()->with('success', 'Invoice has been marked as sent.');
    }

    public function cancel(Invoice $invoice)
    {
        $invoice->update(['status' => 'cancelled']);
        return redirect()->back()->with('success', 'Invoice cancelled successfully.');
    }

    public function markAsPending(Invoice $invoice)
    {
        $invoice->update(['status' => 'pending']);
        return response()->json(['success' => true]);
    }
}
