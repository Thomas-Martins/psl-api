<?php

namespace App\Services;

use App\Models\Order;
use App\Services\PdfService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class OrderDocumentService
{
    private PdfService $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function generateProductsListDownload(Order $order, ?string $locale = null)
    {
        $order->loadMissing(['ordersProducts.product', 'user.store']);

        try {
            $locale = $locale ?? App::getLocale();
            $pdf = $this->pdfService->generatePdfFromView('orders.products_list', [
                'order' => $order,
                'locale' => $locale
            ], $locale);
            return $pdf->download($this->getFileName($order, $locale));
        } catch (\Exception $e) {
            Log::error('Failed to generate products list PDF', [
                'order_id' => $order->id,
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getFileName(Order $order, string $locale): string
    {
        if (!in_array($locale, ['en', 'fr'])) {
            $locale = config('app.locale', 'fr');
        }
        $prefix = trans('products_list.file_prefix', [], $locale);
        return "{$prefix}-{$order->reference}.pdf";
    }
}
