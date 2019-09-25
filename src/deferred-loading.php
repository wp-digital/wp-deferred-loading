<?php

namespace Innocode\WPDeferredLoading;

/**
 * Checks if script should be deferred
 *
 * @param string $handle
 *
 * @return bool
 */
function is_script_deferred( $handle ) {
    $handles = apply_filters( 'deferred_loading_scripts', [] );

    return $handles == '*' || in_array( $handle, $handles );
}

/**
 * Defers script loading
 *
 * @param string $tag
 * @param string $handle
 * @param string $src
 *
 * @return string
 */
function add_attr_defer( $tag, $handle, $src ) {
    return ! is_admin() && ! is_customize_preview() && is_script_deferred( $handle )
        ? str_replace( ' src', ' defer onload="' . get_script_onload( $handle, $tag, $src ) . '" src', $tag )
        : $tag;
}

/**
 * Adds 'onload' attribute, add justify script for jQuery
 *
 * @param string $handle
 * @param string $tag
 * @param string $src
 *
 * @return string
 */
function get_script_onload( $handle, $tag, $src ) {
    global $wp_scripts;

    $queue = $wp_scripts->queue;

    return apply_filters( 'deferred_loading_script_onload',
        wp_script_is( 'jquery' )
        && is_script_deferred( 'jquery-core' )
        && end( $queue ) === $handle
            ? justify_jquery()
            : '',
        $handle, $tag, $src );

}

add_filter( 'script_loader_tag', __NAMESPACE__ . '\add_attr_defer', 10, 3 );

/**
 * Prints falsify jQuery script
 */
function falsify_jquery() {
    if ( wp_script_is( 'jquery' ) && is_script_deferred( 'jquery-core' ) ) : ?>
        <script>
          !function(a,b,c){var d,e;a.bindReadyQ=[],a.bindLoadQ=[],e=function(b,c){switch(b){case"load":a.bindLoadQ.push(c);break;case"ready":a.bindReadyQ.push(c);break;default:a.bindReadyQ.push(b)}},d={load:e,ready:e,bind:e,on:e},a.$=a.jQuery=function(f){return f===b||f===c||f===a?d:void e(f)}}(window,document);
        </script>
    <?php endif;
}

/**
 * Returns justify jQuery script
 *
 * @return string
 */
function justify_jquery() {
    return '!function(a,b){b.isArray(a.bindReadyQ)&&b.each(a.bindReadyQ,function(d,i){b(i)}),b.isArray(a.bindLoadQ)&&b.each(a.bindLoadQ,function(d,i){b(a).on(\'load\',i)})}(window,jQuery);';
}

add_action( 'wp_head', __NAMESPACE__ . '\falsify_jquery', 1 );

/**
 * Checks if style should be deferred
 *
 * @param string $handle
 *
 * @return bool
 */
function is_style_deferred( $handle ) {
    $handles = apply_filters( 'deferred_loading_styles', [] );

    return $handles == '*' || in_array( $handle, $handles );
}

/**
 * Checks if deferred styles exists
 *
 * @return bool
 */
function has_deferred_styles() {
    global $wp_styles;

    $handles = apply_filters( 'deferred_loading_styles', [] );

    return ! empty( $handles ) && ( $handles == '*' || count( array_intersect( $handles, array_keys( $wp_styles->registered ) ) ) > 0 );
}

/**
 * Defers style loading
 *
 * @param string $tag
 * @param string $handle
 *
 * @return string
 */
function add_attr_rel_preload( $tag, $handle ) {
    return ! is_admin() && ! is_customize_preview() && is_style_deferred( $handle )
        ? str_replace( ' rel=\'stylesheet\'', ' rel=\'preload\' as=\'style\' onload=\'this.rel="stylesheet"\'', $tag ) . "<noscript>$tag</noscript>"
        : $tag;
}

add_filter( 'style_loader_tag', __NAMESPACE__ . '\add_attr_rel_preload', 10, 2 );

/**
 * Prints loadCSS.js with 'preload' polyfill
 */
function load_css_script() {
    if ( has_deferred_styles() ) : ?>
        <script>
          !function(e){"use strict";var n=function(n,t,o){function i(e){return a.body?e():void setTimeout(function(){i(e)})}function r(){l.addEventListener&&l.removeEventListener("load",r),l.media=o||"all"}var d,a=e.document,l=a.createElement("link");if(t)d=t;else{var s=(a.body||a.getElementsByTagName("head")[0]).childNodes;d=s[s.length-1]}var f=a.styleSheets;l.rel="stylesheet",l.href=n,l.media="only x",i(function(){d.parentNode.insertBefore(l,t?d:d.nextSibling)});var u=function(e){for(var n=l.href,t=f.length;t--;)if(f[t].href===n)return e();setTimeout(function(){u(e)})};return l.addEventListener&&l.addEventListener("load",r),l.onloadcssdefined=u,u(r),l};"undefined"!=typeof exports?exports.loadCSS=n:e.loadCSS=n}("undefined"!=typeof global?global:this);
          !function(t){if(t.loadCSS){var e=loadCSS.relpreload={};if(e.support=function(){try{return t.document.createElement("link").relList.supports("preload")}catch(e){return!1}},e.poly=function(){for(var e=t.document.getElementsByTagName("link"),n=0;n<e.length;n++){var r=e[n];"preload"===r.rel&&"style"===r.getAttribute("as")&&(t.loadCSS(r.href,r),r.rel=null)}},!e.support()){e.poly();var n=t.setInterval(e.poly,300);t.addEventListener&&t.addEventListener("load",function(){t.clearInterval(n)}),t.attachEvent&&t.attachEvent("onload",function(){t.clearInterval(n)})}}}(this);
        </script>
    <?php endif;
}

add_filter( 'wp_head', __NAMESPACE__ . '\load_css_script', 99 );
