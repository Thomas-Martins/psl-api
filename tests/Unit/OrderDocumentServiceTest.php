<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Services\OrderDocumentService;
use App\Services\PdfService;
use Tests\TestCase;

class OrderDocumentServiceTest extends TestCase
{
    public function test_generate_products_list_download_handles_exception()
    {
        $pdfService = \Mockery::mock(PdfService::class);
        $pdfService->shouldReceive('generatePdfFromView')->andThrow(new \RuntimeException('PDF error'));
        $order = \Mockery::mock(Order::class);
        $order->shouldReceive('setAttribute')->andReturnNull();
        $order->shouldReceive('getAttribute')->andReturn(null);
        $order->shouldReceive('loadMissing')->andReturnSelf();
        $order->id = 1;
        $service = new OrderDocumentService($pdfService);
        $this->expectException(\RuntimeException::class);
        $service->generateProductsListDownload($order);
    }
}
