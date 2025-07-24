<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PdfService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    private PdfService $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }
    /**
     * Generates the invoice PDF and returns it as a stream response
     */
    public function generatePdfResponse(Order $order, string $locale)
    {
        $pdf = $this->generatePdf($order, $locale);
        return $pdf->stream($this->getFileName($order, $locale));
    }

    /**
     * Generates the invoice PDF and returns it as a download response
     */
    public function generatePdfDownload(Order $order, string $locale)
    {
        $pdf = $this->generatePdf($order, $locale);
        return $pdf->download($this->getFileName($order, $locale));
    }

    /**
     * Generates the PDF and returns its content as a binary string
     * Useful for emails or storage
     */
    public function generatePdfString(Order $order, string $locale): string
    {
        $pdf = $this->generatePdf($order, $locale);
        return $pdf->output();
    }

    /**
     * Generates the PDF filename based on the locale
     */
    public function getFileName(Order $order, string $locale): string
    {
        if (!in_array($locale, ['en', 'fr'])) {
            $locale = config('app.locale', 'fr');
        }

        $currentLocale = App::getLocale();

        App::setLocale($locale);

        $prefix = __('invoice.file_prefix');

        App::setLocale($currentLocale);

        return "{$prefix}-{$order->reference}.pdf";
    }

    /**
     * Generates the PDF from the view
     */
    private function generatePdf(Order $order, string $locale)
    {
        if (
            !$order->relationLoaded('ordersProducts') ||
            !$order->relationLoaded('user') ||
            optional($order->user)->relationLoaded('store') === false
        ) {
            $order->loadMissing(['ordersProducts.product', 'user.store']);
        }

        return $this->pdfService->generatePdfFromView('invoices.show', [
            'order' => $order,
            'locale' => $locale
        ], $locale);
    }
}
