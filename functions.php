<?php

/**
 * Helo Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Helo Theme
 * @since 1.0.0
 */

require_once get_stylesheet_directory() . '/inc/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$updateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/matesasesinos/helo-woostify-child.git',
    __FILE__,
    'helo-woostify-child'
);

$updateChecker->setBranch('main');

// functions and other includes
require_once __DIR__ . '/inc/options.php';
require_once __DIR__ . '/inc/customer-special.php';
require_once __DIR__ . '/inc/ajax-api.functions.php';
require_once __DIR__ . '/inc/geoloc.php';
require_once __DIR__ . '/inc/menu.php';
require_once __DIR__ . '/inc/woocommerce.php';

/* Main styles */
add_action('wp_enqueue_scripts', 'helo__enqueue_styles');

function helo__enqueue_styles()
{
    wp_enqueue_style(
        'grand-sunrise-style',
        get_stylesheet_uri()
    );

    wp_enqueue_script('helo-main', get_stylesheet_directory_uri() . '/assets/js/main.js', [], '1.0.5', true);
    wp_localize_script('helo-main', 'helo', [
        'url' => admin_url('admin-ajax.php')
    ]);
}

/*
 * Translates
 * 
**/
add_filter('gettext', 'translate_text_theme', 20, 3);

function translate_text_theme($translated_text, $untranslated_text, $domain)
{
    if (!is_admin()) {
        switch ($untranslated_text) {

            case 'Product Added':
                $translated_text = __('Agregado', 'shopbuilder');
                break;

            case 'Browse Cart':
                $translated_text = __('Ver carrito', 'shopbuilder');
                break;

            case 'Select Options':
                $translated_text = __('Ver opciones', 'woocommerce');
                break;

            case 'Default sorting':
                $translated_text = __('Orden predeterminado', 'woocommerce');
                break;

            case 'Town / City':
                $translated_text = __('Ciudad', 'woocommerce');
                break;

            case 'Billing Details':
                $translated_text = __('Detalles de facturación', 'woocommerce');
                break;

            case 'Dirección de la calle':
                $translated_text = __('Calle y número', 'woocommerce');
                break;

            case 'Subtotal discount':
                $translated_text = __('Descuento', 'woocommerce');
                break;

            case 'Street address':
                $translated_text = __('Calle y número', 'woocommerce');
                break;

            case 'The following problems were found:':
                $translated_text = __('Hay problemas con tu pedido', 'woocommerce');
                break;

            case 'free shipping':
                $translated_text = __('Envío gratis', 'woocommerce');
                break;
        }
    }
    return $translated_text;
}

add_action('wp_footer', function () {
?>
    <script>
        jQuery(document).ready(function($) {
            $('#shipping-postcode').keypress(function(event) {
                var inputValue = event.which;
                if ((inputValue >= 48 && inputValue <= 57) || inputValue == 8) {
                    return true;
                } else {
                    event.preventDefault();
                }
            });
        });
    </script>
<?php
}, 999);

function hide_all_admin_notices()
{
    global $wp_filter;

    // Check if the WP_Admin_Bar exists, as it's not available on all admin pages.
    if (isset($wp_filter['admin_notices'])) {
        // Remove all actions hooked to the 'admin_notices' hook.
        unset($wp_filter['admin_notices']);
    }
}

add_action('admin_init', 'hide_all_admin_notices');


//Error por peso
function weigth_verify()
{
    $option = get_option('helo_mw_option', false);

    if (!$option)
        return true;

    $limite_peso =  get_option('helo_mw_option_weight', '25');
    $peso_total = WC()->cart->get_cart_contents_weight();

    if ($peso_total > $limite_peso) {
        return false;
    }

    return true;
}

add_action('template_redirect', function () {
    if (!is_checkout())
        return;

    if (!weigth_verify()) {
        wp_redirect(wc_get_cart_url());
        exit;
    }
});

add_action('woocommerce_check_cart_items', function () {
    $limite_peso =  get_option('helo_mw_option_weight', '25');
    $error_message = get_option('helo_mw_option_error_message', 'Compra máxima por bulto %skg total, para pesos mayores generar otra compra.');
    if (!weigth_verify()) {
        wc_add_notice(sprintf($error_message, $limite_peso), 'error');
    }
});

function add_alpine_js()
{
    wp_enqueue_script(
        'alpine-js', // Nombre único para el script
        'https://unpkg.com/alpinejs', // URL de Alpine.js
        array(), // Dependencias vacías
        null, // Número de versión (null usa la última versión)
        false // Cargar en el footer
    );

    // Agregar defer al script
    add_filter('script_loader_tag', 'add_defer_alpine', 10, 2);
}

function add_defer_alpine($tag, $handle)
{
    if ('alpine-js' === $handle) {
        return str_replace('src', 'defer="defer" src', $tag);
    }
    return $tag;
}

add_action('wp_enqueue_scripts', 'add_alpine_js');

function topbar_marquee()
{ ?>
    <div class="topbar">
        <div class="woostify-container">
            <div class="topbar-item topbar-left">
                <?php //echo do_shortcode($topbar_left); 
                $top_bar_message = get_option('helo_mw_top_bar_message', 'Envíos a todo el país - 10% de descuento más de 5L - Somos fabricantes - Con tu primera compra, asesoramiento gratuito');
                ?>
            </div>
            <div class="topbar-item topbar-center ">
                <marquee behavior="scroll" scrollamount="4" direction="left"><?php echo $top_bar_message; ?></marquee>

            </div>
            <div class="topbar-item topbar-right"><?php //echo do_shortcode($topbar_right); 
                                                    ?></div>
        </div>
    </div>
<?php }

add_action('woostify_template_part_header', 'topbar_marquee', 20);

// habilitar SVG con sanitización básica
function cc_mime_types($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

function fix_svg()
{
    echo '<style>
        .attachment-266x266, .thumbnail img {
            width: 100% !important;
            height: auto !important;
        }
    </style>';
}
add_action('admin_head', 'fix_svg');
