<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class PdfService
{
    public function generatePdfFromView(string $view, array $data, ?string $locale = null)
    {
        $currentLocale = App::getLocale();
        if ($locale && in_array($locale, ['en', 'fr'])) {
            App::setLocale($locale);
        }

        try {
            $pdf = Pdf::loadView($view, $data);
            $pdf->setPaper('a4');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isPhpEnabled', true);
        } catch (\Exception $e) {
            Log::error('PDF generation failed', [
                'view' => $view,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to generate PDF: ' . $e->getMessage(), 0, $e);
        } finally {
            if ($locale && in_array($locale, ['en', 'fr'])) {
                App::setLocale($currentLocale);
            }
        }

        return $pdf;
    }
}
