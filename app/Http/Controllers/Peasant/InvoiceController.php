<?php

namespace App\Http\Controllers\Peasant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::where('transaction_type', 'purchase')
            ->whereHas('purchase', function ($query) {
                $query->where('peasant_id', Auth::id());
            })
            ->with(['purchase.coffeeType'])
            ->orderBy('invoice_date', 'desc')
            ->paginate(15);

        return view('peasant.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['purchase.coffeeType', 'purchase.peasant']);

        return view('peasant.invoices.show', compact('invoice'));
    }

    /**
     * Devuelve el HTML del detalle de la factura para mostrar en modal (panel campesino).
     */
    public function details(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['purchase.coffeeType', 'purchase.peasant', 'purchase.payments']);

        return view('peasant.invoices.partials.detail-modal', compact('invoice'));
    }

    /**
     * Descargar factura en PDF (solo abre el cuadro "Dónde guardar", no muestra HTML).
     */
    public function download(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
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
            \Illuminate\Support\Facades\Log::error('Error al generar PDF de factura', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->back()->with('error', 'No se pudo generar el PDF. En Laragon: Menu PHP → elige 8.2, reinicia, y en la carpeta AgroCafe ejecuta: composer install --ignore-platform-reqs');
    }

    private function authorizeInvoice(Invoice $invoice): void
    {
        if ($invoice->transaction_type !== 'purchase' || $invoice->purchase->peasant_id !== Auth::id()) {
            abort(403);
        }
    }
}

