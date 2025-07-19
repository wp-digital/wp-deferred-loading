<?php

namespace Innocode\WPDeferredLoading\Tests\Unit;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use function Innocode\WPDeferredLoading\add_attr_rel_preload;

class AddAttrRelPreloadTest extends BaseTestCase
{
    public function testChangeMediaToPrintUsingSingleQuote()
    {
        $tag = '<link rel=\'stylesheet\' id=\'perfect-styles-css\' href=\'https://acme.com/style.css?ver=1.2.3\' media=\'all\' />';
        $handle = 'perfect-styles';
        $href = 'https://acme.com/style.css?ver=1.2.3';

        Functions\expect('is_admin')
            ->once()
            ->andReturn(false);
        Functions\expect('is_customize_preview')
            ->once()
            ->andReturn(false);
        Filters\expectApplied('deferred_loading_styles')
            ->once()
            ->withAnyargs()
            ->andReturn([ $handle ]);

        $this->assertSame(
            "\n<link rel='stylesheet' id='perfect-styles-css' href='https://acme.com/style.css?ver=1.2.3' media='print' onload='this.media=\"all\";this.onload=null;' />\n<noscript>\n<link rel='stylesheet' id='perfect-styles-css' href='https://acme.com/style.css?ver=1.2.3' media='all' />\n</noscript>\n",
            add_attr_rel_preload($tag, $handle, $href, 'all'),
        );
    }

    public function testChangeMediaToPrintUsingDoubleQuote()
    {
        $tag = '<link rel="stylesheet" id="perfect-styles-css" href="https://acme.com/style.css?ver=1.2.3" media="all" />';
        $handle = 'perfect-styles';
        $href = 'https://acme.com/style.css?ver=1.2.3';

        Functions\expect('is_admin')
            ->once()
            ->andReturn(false);
        Functions\expect('is_customize_preview')
            ->once()
            ->andReturn(false);
        Filters\expectApplied('deferred_loading_styles')
            ->once()
            ->withAnyargs()
            ->andReturn([ $handle ]);

        $this->assertSame(
            "\n<link rel=\"stylesheet\" id=\"perfect-styles-css\" href=\"https://acme.com/style.css?ver=1.2.3\" media=\"print\" onload=\"this.media='all';this.onload=null;\" />\n<noscript>\n<link rel=\"stylesheet\" id=\"perfect-styles-css\" href=\"https://acme.com/style.css?ver=1.2.3\" media=\"all\" />\n</noscript>\n",
            add_attr_rel_preload($tag, $handle, $href, 'all'),
        );
    }

    public function testNoChangeMediaToPrintWhenNotDeferred()
    {
        $tag = '<link rel="stylesheet" id="perfect-styles-css" href="https://acme.com/style.css?ver=1.2.3" media="all" />';
        $handle = 'perfect-styles';
        $href = 'https://acme.com/style.css?ver=1.2.3';

        Functions\expect('is_admin')
            ->once()
            ->andReturn(false);
        Functions\expect('is_customize_preview')
            ->once()
            ->andReturn(false);
        Filters\expectApplied('deferred_loading_styles')
            ->once()
            ->withAnyargs()
            ->andReturn([]);

        $this->assertSame(
            $tag,
            add_attr_rel_preload($tag, $handle, $href, 'all'),
        );
    }
}
