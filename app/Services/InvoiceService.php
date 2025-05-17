<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * Génère le PDF de la facture et le retourne comme une réponse de type stream
     */
    public function generatePdfResponse(Order $order, string $locale)
    {
        $pdf = $this->generatePdf($order, $locale);
        return $pdf->stream("facture-{$order->reference}.pdf");
    }

    /**
     * Génère le PDF de la facture et le retourne comme une réponse de téléchargement
     */
    public function generatePdfDownload(Order $order, string $locale)
    {
        $pdf = $this->generatePdf($order, $locale);
        return $pdf->download("facture-{$order->reference}.pdf");
    }

    /**
     * Génère le PDF et retourne son contenu sous forme de chaîne binaire
     * Utile pour les emails ou le stockage
     */
    public function generatePdfString(Order $order, string $locale): string
    {
        $pdf = $this->generatePdf($order, $locale);
        return $pdf->output();
    }

    /**
     * Génère le PDF à partir de la vue
     */
    private function generatePdf(Order $order, string $locale)
    {
        if (!$order->relationLoaded('ordersProducts') ||
            !$order->relationLoaded('user') ||
            !$order->user->relationLoaded('store')) {
            $order->load(['ordersProducts.product', 'user.store']);
        }

        $currentLocale = App::getLocale();

        Log::info('Invoice service locale', [
            'received_locale' => $locale,
            'current_locale' => $currentLocale,
            'order_id' => $order->id
        ]);

        if ($locale && in_array($locale, ['en', 'fr'])) {
            App::setLocale($locale);
            Log::info('Setting locale to: ' . $locale);
        } else {
            Log::warning('Invalid or missing locale: ' . ($locale ?? 'null') . ', using default: ' . $currentLocale);
        }

        $pdf = PDF::loadView('invoices.show', [
            'order' => $order,
            'locale' => $locale ?? $currentLocale
        ]);

        $pdf->setPaper('a4');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isPhpEnabled', true);

        if ($locale && in_array($locale, ['en', 'fr'])) {
            App::setLocale($currentLocale);
        }

        return $pdf;
    }

    /**
     * Retourne la vue HTML de la facture (pour prévisualisation)
     */
    public function generateHtml(Order $order, string $locale)
    {
        if (!$order->relationLoaded('ordersProducts') ||
            !$order->relationLoaded('user') ||
            !$order->user->relationLoaded('store')) {
            $order->load(['ordersProducts.product', 'user.store']);
        }

        $currentLocale = App::getLocale();

        Log::info('Invoice HTML view locale', [
            'received_locale' => $locale,
            'current_locale' => $currentLocale,
            'order_id' => $order->id
        ]);

        if ($locale && in_array($locale, ['en', 'fr'])) {
            App::setLocale($locale);
            Log::info('Setting locale to: ' . $locale);
        } else {
            Log::warning('Invalid or missing locale: ' . ($locale ?? 'null') . ', using default: ' . $currentLocale);
        }

        $view = view('invoices.show', [
            'order' => $order,
            'locale' => $locale ?? $currentLocale
        ]);

        if ($locale && in_array($locale, ['en', 'fr'])) {
            App::setLocale($currentLocale);
        }

        return $view;
    }
}
