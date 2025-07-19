<?php

namespace Innocode\WPDeferredLoading\Tests\Unit;

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function setUp(): void
    {
        parent::setUp();

        Monkey\Functions\when('esc_attr')->returnArg();
        Monkey\Functions\when('esc_js')->returnArg();

        global $wp_scripts;

        if (!isset($wp_scripts)) {
            $wp_scripts = (object) [
                'queue' => [
                    'jquery-core',
                ],
            ];
        }
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
