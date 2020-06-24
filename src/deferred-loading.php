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
        ? str_replace(
            " src='$src'",
            ' defer onload=\'' . get_script_onload( $handle, $tag, $src ) . "' src='$src'",
            $tag
        )
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

    return apply_filters(
        'deferred_loading_script_onload',
        wp_script_is( 'jquery' ) && is_script_deferred( 'jquery-core' ) && end( $queue ) === $handle
            ? justify_jquery()
            : '',
        $handle,
        $tag,
        $src
    );
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
    return '!function(a,b){b.isArray(a.bindReadyQ)&&b.each(a.bindReadyQ,function(d,i){b(i)}),b.isArray(a.bindLoadQ)&&b.each(a.bindLoadQ,function(d,i){b(a).on("load",i)})}(window,jQuery);';
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

    return ! empty( $handles ) &&
        (
            $handles == '*' ||
            count( array_intersect( $handles, array_keys( $wp_styles->registered ) ) ) > 0
        );
}

/**
 * Defers style loading
 *
 * @param string $tag
 * @param string $handle
 * @param string $href
 * @param string $media
 *
 * @return string
 */
function add_attr_rel_preload( $tag, $handle, $href, $media ) {
    return ! is_admin() && ! is_customize_preview() && $media != 'print' && is_style_deferred( $handle )
        ? str_replace(
                " media='$media'",
                " media='print' onload='this.media=\"$media\";this.onload=null;'",
                $tag
        ) . "<noscript>\n$tag</noscript>\n"
        : $tag;
}

add_filter( 'style_loader_tag', __NAMESPACE__ . '\add_attr_rel_preload', 10, 4 );
