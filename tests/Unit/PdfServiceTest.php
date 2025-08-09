<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PdfService;
use Mockery;

class PdfServiceTest extends TestCase
{
    public function test_generate_pdf_from_view_returns_pdf_object()
    {
        $service = new PdfService();
        $order = new class {
            public $reference = 'ORD-1234';
            public $created_at;
            public $user;
            public $ordersProducts = [];
            const TAX_RATE = 0.2;
            public function __construct()
            {
                $this->created_at = now();
                $this->user = new class {
                    public $identity = 'JD123';
                    public $firstname = 'John';
                    public $lastname = 'Doe';
                    public $email = 'john@example.com';
                    public $phone = '0601020304';
                    public $store;
                    public function __construct()
                    {
                        $this->store = new class {
                            public $name = 'Test Store';
                            public $full_address = '123 Main St';
                        };
                    }
                };
            }
            public function calculateSubtotal()
            {
                return 100.0;
            }
            public function calculateTax()
            {
                return 20.0;
            }
            public function calculateTotal()
            {
                return 120.0;
            }
        };
        $pdf = $service->generatePdfFromView('invoices.show', ['order' => $order, 'locale' => 'fr'], 'fr');
        $this->assertNotNull($pdf);
    }
}
