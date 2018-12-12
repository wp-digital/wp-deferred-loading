## Deferred loading

### Description

WordPress plugin for deferred loading of JavaScript and CSS files.

### Install

Clone this repo to `wp-content/plugins/`:

````
cd wp-content/plugins/
git clone git@github.com:innocode-digital/wp-deferred-loading.git
````

Activate **Deferred loading** from Plugins page 
or [WP-CLI](https://make.wordpress.org/cli/handbook/): `wp plugin activate wp-deferred-loading`.

Also you could install it as a [Must Use Plugin](https://codex.wordpress.org/Must_Use_Plugins).

### Usage

To defer JavaScript files, add to `functions.php` of theme:

````
add_filter( 'deferred_loading_scripts', function () {
    return [
        // List of enqueued scripts.
    ];
} );
````

or

````
add_filter( 'deferred_loading_scripts', function () {
    return '*'; // All enqueued scripts.
} );
````

To defer CSS files, add to `functions.php` of theme:

````
add_filter( 'deferred_loading_styles', function () {
    return [
        // List of enqueued styles.
    ];
} );
````