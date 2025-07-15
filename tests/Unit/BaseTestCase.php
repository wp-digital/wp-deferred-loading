<?php

namespace Innocode\WPDeferredLoading\Tests\Unit;

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
