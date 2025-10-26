<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContractController extends Controller
{
    /**
     * 🧾 Get all contracts (for admin dashboard API)
     */
    public function index()
    {
        $contracts = Contract::with(['tenant', 'unit'])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($contracts);
    }

    /**
     * 🏠 Show all contracts in admin view (Blade)
     */
    public function showContracts()
    {
        return view('admin.contracts');
    }

    /**
     * ➕ Create a new contract record
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|exists:tenants,id',
            'unit_id' => 'required|exists:units,id',
            'contract_start' => 'required|date',
            'contract_end' => 'required|date|after_or_equal:contract_start',
            'contract_duration' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'annual_interest' => 'nullable|numeric|min:0',
            'downpayment' => 'nullable|numeric|min:0',
            'monthly_payment' => 'nullable|numeric|min:0',
            'payment_due_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'status' => 'nullable|string',
            'contract_pdf' => 'nullable|mimes:pdf|max:2048',
            'invoice_pdf' => 'nullable|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 🧾 Handle file uploads
        $contractPdfPath = null;
        $invoicePdfPath = null;

        if ($request->hasFile('contract_pdf')) {
            $contractPdfPath = $request->file('contract_pdf')->store('contracts', 'public');
        }

        if ($request->hasFile('invoice_pdf')) {
            $invoicePdfPath = $request->file('invoice_pdf')->store('invoices', 'public');
        }

        $contract = Contract::create([
            'tenant_id' => $request->tenant_id,
            'unit_id' => $request->unit_id,
            'contract_start' => $request->contract_start,
            'contract_end' => $request->contract_end,
            'contract_duration' => $request->contract_duration,
            'unit_price' => $request->unit_price,
            'total_price' => $request->total_price,
            'annual_interest' => $request->annual_interest,
            'downpayment' => $request->downpayment,
            'monthly_payment' => $request->monthly_payment,
            'payment_due_date' => $request->payment_due_date,
            'last_billed_at' => $request->last_billed_at,
            'next_due_date' => $request->next_due_date,
            'status' => $request->status ?? 'Active',
            'remarks' => $request->remarks,
            'contract_pdf' => $contractPdfPath,
            'invoice_pdf' => $invoicePdfPath,
        ]);

        return response()->json([
            'message' => 'Contract created successfully',
            'contract' => $contract
        ], 201);
    }

    /**
     * 🖊 Update contract info
     */
    public function update(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'contract_end' => 'nullable|date|after_or_equal:contract_start',
            'contract_duration' => 'nullable|numeric|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'total_price' => 'nullable|numeric|min:0',
            'annual_interest' => 'nullable|numeric|min:0',
            'downpayment' => 'nullable|numeric|min:0',
            'monthly_payment' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'status' => 'nullable|string',
            'contract_pdf' => 'nullable|mimes:pdf|max:2048',
            'invoice_pdf' => 'nullable|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update PDFs if uploaded
        if ($request->hasFile('contract_pdf')) {
            if ($contract->contract_pdf) {
                Storage::disk('public')->delete($contract->contract_pdf);
            }
            $contract->contract_pdf = $request->file('contract_pdf')->store('contracts', 'public');
        }

        if ($request->hasFile('invoice_pdf')) {
            if ($contract->invoice_pdf) {
                Storage::disk('public')->delete($contract->invoice_pdf);
            }
            $contract->invoice_pdf = $request->file('invoice_pdf')->store('invoices', 'public');
        }

        $contract->update($request->except(['contract_pdf', 'invoice_pdf']));

        return response()->json([
            'message' => 'Contract updated successfully',
            'contract' => $contract
        ]);
    }

    /**
     * 🗑 Delete (soft delete) a contract
     */
    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);
        $contract->delete();

        return response()->json(['message' => 'Contract archived successfully']);
    }

    /**
     * 🧠 Restore soft-deleted contract
     */
    public function restore($id)
    {
        $contract = Contract::withTrashed()->findOrFail($id);
        $contract->restore();

        return response()->json(['message' => 'Contract restored successfully']);
    }
}
