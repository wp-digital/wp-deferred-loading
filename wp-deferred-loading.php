<?php
/**
 * Plugin Name: Deferred loading
 * Description: Defer loading of JavaScript and CSS files.
 * Version: 0.0.1
 * Author: Innocode
 * Author URI: https://innocode.com
 * Requires at least: 4.8
 * Tested up to: 4.8.3
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

define( 'DEFERRED_LOADING_VERSION', '0.0.1' );

/**
 * @param string $key
 *
 * @return string
 */
function deferred_loading_key( $key ) {
    return 'deferred_loading_' . sanitize_key( $key );
}

/**
 * @param string $handle
 *
 * @return bool
 */
function deferred_loading_is_script_deferred( $handle ) {
    $handles = apply_filters( deferred_loading_key( 'scripts' ), [] );

    return $handles == '*' || in_array( $handle, $handles );
}

/**
 * @param string $tag
 * @param string $handle
 * @param string $src
 *
 * @return string
 */
function deferred_loading_add_attr_defer( $tag, $handle, $src ) {
    return !is_admin() && deferred_loading_is_script_deferred( $handle )
        ? str_replace( ' src', ' defer onload="' . deferred_loading_get_script_onload( $handle, $tag, $src ) . '" src', $tag )
        : $tag;
}

/**
 * @param string $handle
 * @param string $tag
 * @param string $src
 *
 * @return string
 */
function deferred_loading_get_script_onload( $handle, $tag, $src ) {
    return apply_filters( deferred_loading_key( 'script_onload' ), $handle == 'jquery-core' ? deferred_loading_justify_jquery() : '', $handle, $tag, $src );
}

add_filter( 'script_loader_tag', 'deferred_loading_add_attr_defer', 10, 3 );

function deferred_loading_falsify_jquery() {
    if ( deferred_loading_is_script_deferred( 'jquery-core' ) ) : ?>
        <script>!function(a,b,c){var d,e;a.bindReadyQ=[],a.bindLoadQ=[],e=function(b,c){switch(b){case"load":a.bindLoadQ.push(c);break;case"ready":a.bindReadyQ.push(c);break;default:a.bindReadyQ.push(b)}},d={load:e,ready:e,bind:e,on:e},a.$=a.jQuery=function(f){return f===b||f===c||f===a?d:void e(f)}}(window,document);</script>
    <?php endif;
}

/**
 * @return string
 */
function deferred_loading_justify_jquery() {
    return '!function(a,b){b.isArray(a.bindReadyQ)&&b.each(a.bindReadyQ,function(d,i){b(i)}),b.isArray(a.bindLoadQ)&&b.each(a.bindLoadQ,function(d,i){b(a).on(\'load\',i)})}(window,jQuery);';
}

add_action( 'wp_head', 'deferred_loading_falsify_jquery', 1 );