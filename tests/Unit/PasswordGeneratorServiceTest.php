<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PasswordGeneratorService;

class PasswordGeneratorServiceTest extends TestCase
{
    public function test_generate_returns_string_of_given_length()
    {
        $service = new PasswordGeneratorService();
        $password = $service->generate(12);
        $this->assertIsString($password);
        $this->assertEquals(12, strlen($password));
    }
}
