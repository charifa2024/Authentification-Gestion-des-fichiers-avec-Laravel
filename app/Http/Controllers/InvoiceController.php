<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Correct namespace for Storage
use App\Models\Invoice;
use App\Mail\InvoiceCreated;
use Illuminate\Support\Facades\Mail as MailFacade;

class InvoiceController extends Controller
{
    // Display list of invoices
    public function index()
    {
        $invoices = Invoice::all();

        return view('invoices.index', compact('invoices'));
    }

    // Show the form to create an invoice
    public function create()
    {
        return view('invoices.create');
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'client_name' => 'required|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'in:unpaid,paid,canceled',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);
    
        // Handle file upload
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('invoices', 'public');
            $validated['file_path'] = $filePath;
        }
        // Create the invoice
        $invoice = Invoice::create($validated);
        // Send the email
        MailFacade::to('charifac713@gmail.com')->send(new InvoiceCreated($invoice));        return redirect()->route('invoices.index')->with('success', 'Facture créée avec succès.');
    }
    
    public function edit($id){
    $invoice = \App\Models\Invoice::findOrFail($id);
    return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, $id)
    {
        // Find the invoice or throw a 404 if not found
        $invoice = \App\Models\Invoice::findOrFail($id);
    
        // Validate the input data including the file
        $validated = $request->validate([
            'client_name' => 'required|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'in:unpaid,paid,canceled',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:2048', // File validation
        ]);
    
        // Handle file upload if a new file is uploaded
        if ($request->hasFile('file')) {
            // Delete the old file if it exists
            if ($invoice->file_path) {
                Storage::disk('public')->delete($invoice->file_path); // Delete old file
            }
    
            // Store the new file in "storage/app/public/invoices"
            $filePath = $request->file('file')->store('invoices', 'public');
            $validated['file_path'] = $filePath; // Save new file path
        }
    
        // Update the invoice with validated data
        $invoice->update($validated);
    
        // Redirect with success message
        return redirect()->route('invoices.index')->with('success', 'Facture mise à jour avec succès.');
    }

    public function destroy($id){
    // Find the invoice or throw a 404 error if not found
    $invoice = \App\Models\Invoice::findOrFail($id);

    // Delete the associated file if it exists
    if ($invoice->file_path) {
        Storage::disk('public')->delete($invoice->file_path); // Delete from the 'public' disk
    }

    // Delete the invoice record from the database
    $invoice->delete();

    // Redirect back with a success message
    return redirect()->route('invoices.index')->with('success', 'Facture supprimée avec succès.');
}

    
    
}
