<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Cooperative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['sale', 'purchase']);

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->get();

        return view('admin.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['sale.coffeeType', 'sale.cooperative', 'purchase.coffeeType', 'purchase.peasant']);

        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['sale.coffeeType', 'sale.cooperative', 'purchase.coffeeType', 'purchase.peasant']);
        $cooperatives = Cooperative::active()->get();
        
        return view('admin.invoices.edit', compact('invoice', 'cooperatives'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:20|unique:invoices,invoice_number,' . $invoice->id,
            'invoice_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'taxes' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,paid,overdue',
            'due_date' => 'nullable|date',
        ]);

        $invoice->update($validated);

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Factura actualizada exitosamente.');
    }

    public function generate(Invoice $invoice)
    {
        $invoice->load(['sale.coffeeType', 'sale.cooperative', 'purchase.coffeeType', 'purchase.peasant']);
        $filename = 'factura-' . preg_replace('/[^a-zA-Z0-9\-]/', '_', $invoice->invoice_number) . '.pdf';

        try {
            if (class_exists(\Dompdf\Dompdf::class)) {
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml(view('admin.invoices.pdf', compact('invoice'))->render());
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                return response()->streamDownload(
                    fn () => print($dompdf->output()),
                    $filename,
                    ['Content-Type' => 'application/pdf']
                );
            }

            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                return \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.invoices.pdf', compact('invoice'))
                    ->download($filename);
            }
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de factura', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->back()->with('error', 'No se pudo generar el PDF. En Laragon: Menu PHP → elige 8.2, reinicia el servidor, y en la carpeta AgroCafe ejecuta: composer install --ignore-platform-reqs');
    }

    public function sendEmail(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $invoice->load(['sale.coffeeType', 'sale.cooperative', 'purchase.coffeeType', 'purchase.peasant']);

        // Obtener el email del cliente
        $clientEmail = $validated['email'];
        
        // Obtener el nombre del cliente
        $clientName = '';
        if ($invoice->transaction_type === 'purchase') {
            $clientName = $invoice->purchase->peasant->name ?? 'Campesino';
        } else {
            $clientName = $invoice->sale->cooperative->name ?? $invoice->sale->client_name ?? 'Cliente';
        }

        try {
            // Usa la configuración de .env (MAIL_MAILER, MAIL_USERNAME, MAIL_PASSWORD, etc.)
            Mail::send('emails.invoice', [
                'invoice' => $invoice,
                'clientName' => $clientName,
            ], function ($message) use ($clientEmail, $clientName, $invoice) {
                $message->to($clientEmail, $clientName)
                        ->subject('Factura ' . $invoice->invoice_number . ' - AgroCafé');
            });

            // Si llegamos aquí, el correo se envió exitosamente
            Log::info('Factura enviada por correo exitosamente', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'email' => $clientEmail,
                'client_name' => $clientName
            ]);

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Factura ' . $invoice->invoice_number . ' enviada por correo exitosamente a ' . $clientEmail);
        } catch (\Exception $e) {
            Log::error('Excepción al enviar factura por correo', [
                'invoice_id' => $invoice->id,
                'email' => $clientEmail,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->route('admin.invoices.index')
                ->with('error', 'Error al enviar el correo: ' . $e->getMessage() . '. Revisa los logs para más detalles.');
        }
    }
}

