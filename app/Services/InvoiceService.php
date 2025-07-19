<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
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
        // Ensure all required relations are loaded safely
        if (
            !$order->relationLoaded('ordersProducts') ||
            !$order->relationLoaded('user') ||
            optional($order->user)->relationLoaded('store') === false
        ) {
            $order->loadMissing(['ordersProducts.product', 'user.store']);
        }

        // Set temporary locale for this request
        $currentLocale = App::getLocale();

        Log::info('Invoice service locale', [
            'received_locale' => $locale,
            'current_locale' => $currentLocale,
            'order_id' => $order->id
        ]);

        // Ensure locale is valid and available
        if (in_array($locale, ['en', 'fr'])) {
            App::setLocale($locale);
            Log::info('Setting locale to: ' . $locale);
        } else {
            Log::warning('Invalid locale: ' . $locale . ', using default: ' . $currentLocale);
        }

        try {
            // Specific PDF configuration
            $pdf = PDF::loadView('invoices.show', [
                'order' => $order,
                'locale' => $locale ?? $currentLocale
            ]);

            // Options to improve quality and compatibility
            $pdf->setPaper('a4');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isPhpEnabled', true);
        } catch (\Exception $e) {
            Log::error('PDF generation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to generate invoice PDF: ' . $e->getMessage(), 0, $e);
        } finally {
            // Restore original locale
            if (in_array($locale, ['en', 'fr'])) {
                App::setLocale($currentLocale);
            }
        }

        return $pdf;
    }

}
