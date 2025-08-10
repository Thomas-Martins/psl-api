<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PdfService;
use Mockery;

class PdfServiceTest extends TestCase
{
    public function test_generate_pdf_from_view_mocks_pdf_facade_and_resets_locale()
    {
        // Arrange
        $service = new PdfService();
        $order = Mockery::mock();
        $view = 'invoices.show';
        $data = ['order' => $order, 'locale' => 'fr'];
        $locale = 'fr';
        $originalLocale = app()->getLocale();

        // Mock the PDF facade (or the underlying generator)
        $pdfMock = Mockery::mock();
        $pdfMock->shouldReceive('loadView')->andReturnSelf();
        $pdfMock->shouldReceive('setPaper')->with('a4')->andReturnSelf();
        $pdfMock->shouldReceive('setOption')->with('isHtml5ParserEnabled', true)->andReturnSelf();
        $pdfMock->shouldReceive('download')->andReturn('fake-pdf-content');

        // Swap the facade in the container
        app()->instance('dompdf.wrapper', $pdfMock);

        // Act
        $pdf = $service->generatePdfFromView($view, $data, $locale);
        $result = $pdf->download();

        // Assert
        $this->assertEquals('fake-pdf-content', $result);
        $this->assertEquals($originalLocale, app()->getLocale(), 'Locale should be reset after PDF generation');
    }
}
