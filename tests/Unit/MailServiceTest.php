<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\MailService;

class MailServiceTest extends TestCase
{
    public function test_send_returns_false_on_invalid_view()
    {
        $service = new \App\Services\MailService();
        $result = $service->send('test@example.com', 'Subject', 'invalid_view');
        $this->assertFalse($result);
    }
}
