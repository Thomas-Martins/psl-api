<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\InvoiceService;
use App\Models\Order;
use App\Services\PdfService;
use Mockery;

class InvoiceServiceTest extends TestCase
{
    public function test_generate_pdf_string_returns_binary()
    {
        $pdfMock = new class {
            public function output()
            {
                return 'PDF_BINARY_DATA';
            }
        };
        $pdfService = Mockery::mock(PdfService::class);
        $pdfService->shouldReceive('generatePdfFromView')->andReturn($pdfMock);
        $order = Mockery::mock(Order::class);
        $order->shouldReceive('relationLoaded')->andReturn(true);
        $order->shouldReceive('getAttribute')->andReturn(null);
        $service = new InvoiceService($pdfService);
        $result = $service->generatePdfString($order, 'fr');
        $this->assertIsString($result);
        $this->assertEquals('PDF_BINARY_DATA', $result);
    }
}
