<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\Invoice;
use App\Models\ServiceBooking;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index($id)
    {
        $work = ServiceBooking::findOrFail($id);
        $work->load('invoices');
        return view('admin.work.invoice.index', compact('work'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required',
            'amount' => 'required|numeric',
            'service_booking_id' => 'required|exists:service_bookings,id',
            'img' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,docx|max:5120',
        ]);
        
        $invoiceid = time() . $request->service_booking_id;
        $validatedData['invoiceid'] = $invoiceid;

        if ($request->hasFile('img')) { 
            $file = $request->file('img'); 
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $storagePath = public_path('images/invoices');
            $file->move($storagePath, $filename);
            $validatedData['img'] = 'images/invoices/' . $filename;
        }
        
        Invoice::create($validatedData);

        return response()->json(['message' => 'Invoice created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validatedData = $request->validate([
            'date' => 'required',
            'amount' => 'required|numeric',
            'service_booking_id' => 'required|exists:service_bookings,id',
            'img' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,docx|max:5120',
        ]);

        $previousImagePath = $invoice->img;

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            
            $storagePath = public_path('images/invoices');
            $file->move($storagePath, $filename);
            $validatedData['img'] = 'images/invoices/' . $filename;
            
            if ($previousImagePath) {
                $oldImagePath = public_path($previousImagePath);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }

        $invoice->update($validatedData);

        return response()->json(['message' => 'Invoice updated successfully.']);
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->img) {
            $imagePath = public_path($invoice->img);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $invoice->delete();

        return back()->with('success', 'Invoice deleted successfully.');
    }

    public function showInvoice(ServiceBooking $serviceBooking)
    {
        $invoices = $serviceBooking->invoices;
        return view('user.invoice.details', compact('invoices'));
    }

    public function showInvoiceApi(Work $work)
    {
        $invoice = $work->invoice;
        return response()->json(['invoice' => $invoice]);
    }

}
