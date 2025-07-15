<?php

namespace Innocode\WPDeferredLoading\Tests\Unit;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use function Innocode\WPDeferredLoading\add_attr_defer;

class AddAttrDeferTest extends BaseTestCase
{
    public function testAddAttrDeferUsingSingleQuote()
    {
        $tag = '<script src="https://acme.com/script.js?ver=1.2.3" id="super-script-js"></script>';
        $handle = 'super-script';
        $src = 'https://acme.com/script.js?ver=1.2.3';

        Functions\expect('is_admin')
            ->once()
            ->andReturn(false);
        Functions\expect('is_customize_preview')
            ->once()
            ->andReturn(false);
        Filters\expectApplied('deferred_loading_scripts')
            ->times(2)
            ->withAnyargs()
            ->andReturn([ $handle ]);
        Functions\expect('wp_script_is')
            ->once()
            ->with('jquery')
            ->andReturn(true);

        $this->assertSame(
            '<script defer onload="" src="https://acme.com/script.js?ver=1.2.3" id="super-script-js"></script>',
            add_attr_defer($tag, $handle, $src),
        );
    }

    public function testAddAttrDeferUsingDoubleQuote()
    {
        $tag = '<script src="https://acme.com/script.js?ver=1.2.3" id="super-script-js"></script>';
        $handle = 'super-script';
        $src = 'https://acme.com/script.js?ver=1.2.3';

        Functions\expect('is_admin')
            ->once()
            ->andReturn(false);
        Functions\expect('is_customize_preview')
            ->once()
            ->andReturn(false);
        Filters\expectApplied('deferred_loading_scripts')
            ->times(2)
            ->withAnyargs()
            ->andReturn([ $handle ]);
        Functions\expect('wp_script_is')
            ->once()
            ->with('jquery')
            ->andReturn(true);

        $this->assertSame(
            '<script defer onload="" src="https://acme.com/script.js?ver=1.2.3" id="super-script-js"></script>',
            add_attr_defer($tag, $handle, $src),
        );
    }

    public function testAttAttrDeferWithNoDefer()
    {
        $tag = '<script src="https://acme.com/script.js?ver=1.2.3" id="super-script-js"></script>';
        $handle = 'super-script';
        $src = 'https://acme.com/script.js?ver=1.2.3';

        Functions\expect('is_admin')
            ->once()
            ->andReturn(true);

        $this->assertSame(
            '<script src="https://acme.com/script.js?ver=1.2.3" id="super-script-js"></script>',
            add_attr_defer($tag, $handle, $src),
        );
    }

    public function testAttrDeferForJquery()
    {
        $tag = '<script src="https://acme.com/jquery.js?ver=1.2.3" id="jquery-core-js"></script>';
        $handle = 'jquery-core';
        $src = 'https://acme.com/jquery.js?ver=1.2.3';

        Functions\expect('is_admin')
            ->once()
            ->andReturn(false);
        Functions\expect('is_customize_preview')
            ->once()
            ->andReturn(false);
        Filters\expectApplied('deferred_loading_scripts')
            ->times(2)
            ->withAnyargs()
            ->andReturn([ $handle ]);
        Functions\expect('wp_script_is')
            ->once()
            ->with('jquery')
            ->andReturn(true);

        $this->assertSame(
            '<script defer onload="!function(a,b){b.isArray(a.bindReadyQ)&&b.each(a.bindReadyQ,function(d,i){b(i)}),b.isArray(a.bindLoadQ)&&b.each(a.bindLoadQ,function(d,i){b(a).on("load",i)})}(window,jQuery);" src="https://acme.com/jquery.js?ver=1.2.3" id="jquery-core-js"></script>',
            add_attr_defer($tag, $handle, $src),
        );
    }
}