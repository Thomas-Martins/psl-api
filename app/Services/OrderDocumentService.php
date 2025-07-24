<?php

namespace App\Services;

use App\Models\Order;
use App\Services\PdfService;
use Illuminate\Support\Facades\App;

class OrderDocumentService
{
    private PdfService $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function generateProductsListDownload(Order $order, ?string $locale = null)
    {
        if (
            !$order->relationLoaded('ordersProducts') ||
            !$order->relationLoaded('user') ||
            optional($order->user)->relationLoaded('store') === false
        ) {
            $order->loadMissing(['ordersProducts.product', 'user.store']);
        }

        $locale = $locale ?? App::getLocale();
        $pdf = $this->pdfService->generatePdfFromView('orders.products_list', [
            'order' => $order,
            'locale' => $locale
        ], $locale);
        return $pdf->download($this->getFileName($order, $locale));
    }

    public function getFileName(Order $order, string $locale): string
    {
        if (!in_array($locale, ['en', 'fr'])) {
            $locale = config('app.locale', 'fr');
        }
        $currentLocale = App::getLocale();
        App::setLocale($locale);
        $prefix = __('products_list.file_prefix');
        App::setLocale($currentLocale);
        return "{$prefix}-{$order->reference}.pdf";
    }
}
