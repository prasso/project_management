<?php

namespace Prasso\ProjectManagement\Http\Controllers;

use Prasso\ProjectManagement\Models\Invoice;
use Prasso\ProjectManagement\Models\Client;
use Prasso\ProjectManagement\Models\Project;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('client')->latest()->paginate(10);
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
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

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
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:invoice_items,id',
            'items.*.project_id' => 'nullable|exists:projects,id',
            'items.*.task_id' => 'nullable|exists:tasks,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        $invoice->update([
            'client_id' => $validated['client_id'],
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'tax_rate' => $validated['tax_rate'] ?? 0,
            'notes' => $validated['notes'],
        ]);

        // Delete removed items
        $keepIds = collect($validated['items'])->pluck('id')->filter()->all();
        $invoice->items()->whereNotIn('id', $keepIds)->delete();

        // Update or create items
        foreach ($validated['items'] as $item) {
            if (!empty($item['id'])) {
                $invoice->items()->find($item['id'])->update($item);
            } else {
                $invoice->items()->create($item);
            }
        }

        $invoice->calculateTotals()->save();

        return redirect()->route('prasso-pm.invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('prasso-pm.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid']);
        return redirect()->back()->with('success', 'Invoice marked as paid.');
    }

    public function markAsSent(Invoice $invoice)
    {
        $invoice->update(['status' => 'sent']);
        return redirect()->back()->with('success', 'Invoice marked as sent.');
    }
}
