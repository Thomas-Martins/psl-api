<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\MailService;
use Illuminate\Support\Facades\Mail;

class MailServiceTest extends TestCase
{
    public function test_send_returns_false_on_invalid_view()
    {
        Mail::shouldReceive('send')->andThrow(new \Exception('Mail send failed'));
        $service = new MailService();
        $result = $service->send('test@example.com', 'Subject', 'invalid_view');
        $this->assertFalse($result);
    }
}
