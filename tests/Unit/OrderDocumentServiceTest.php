<?php

namespace Tests\Unit;

use Tests\TestCase;

class OrderDocumentServiceTest extends TestCase
{
    public function test_generate_products_list_download_handles_exception()
    {
        $pdfService = \Mockery::mock(\App\Services\PdfService::class);
        $pdfService->shouldReceive('generatePdfFromView')->andThrow(new \Exception('PDF error'));
        $order = \Mockery::mock(\App\Models\Order::class);
        $service = new \App\Services\OrderDocumentService($pdfService);
        $this->expectException(\Exception::class);
        $service->generateProductsListDownload($order);
    }
}
