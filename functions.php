<?php

/**
 * Helo Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Helo Theme
 * @since 1.0.0
 */

require_once __DIR__ . '/inc/updater.php';
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
function verificar_peso_envio()
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

    if (!verificar_peso_envio()) {
        wp_redirect(wc_get_cart_url());
        exit;
    }
});

add_action('woocommerce_check_cart_items', function () {
    $limite_peso =  get_option('helo_mw_option_weight', '25');
    $error_message = get_option('helo_mw_option_error_message', 'Compra máxima por bulto %skg total, para pesos mayores generar otra compra.');
    if (!verificar_peso_envio()) {

        wc_add_notice(sprintf($error_message, $limite_peso), 'error');
    }
});

function agregar_alpine_js()
{
    wp_enqueue_script(
        'alpine-js', // Nombre único para el script
        'https://unpkg.com/alpinejs', // URL de Alpine.js
        array(), // Dependencias vacías
        null, // Número de versión (null usa la última versión)
        false // Cargar en el footer
    );

    // Agregar defer al script
    add_filter('script_loader_tag', 'agregar_defer_a_alpine', 10, 2);
}

function agregar_defer_a_alpine($tag, $handle)
{
    if ('alpine-js' === $handle) {
        return str_replace('src', 'defer="defer" src', $tag);
    }
    return $tag;
}

add_action('wp_enqueue_scripts', 'agregar_alpine_js');

function topbarMarquee()
{ ?>
    <div class="topbar">
        <div class="woostify-container">
            <div class="topbar-item topbar-left"><?php //echo do_shortcode($topbar_left); 
                                                    ?></div>
            <div class="topbar-item topbar-center ">
                <marquee behavior="scroll" scrollamount="4" direction="left"><?php echo get_option('woostify_setting')['topbar_center']; ?></marquee>

            </div>
            <div class="topbar-item topbar-right"><?php //echo do_shortcode($topbar_right); 
                                                    ?></div>
        </div>
    </div>
<?php }

add_action('woostify_template_part_header', 'topbarMarquee', 20);

//Precios sin impuestos
add_action('wp_footer', 'mostrar_precio_sin_iva_mejorado_despues_de_precio');
function mostrar_precio_sin_iva_mejorado_despues_de_precio()
{
    if (!is_product()) return;
?>
    <style>
        .precio-sin-iva-detalles {
            font-size: 1em;
            color: #555;
            line-height: 1.4;
        }

        .precio-sin-iva-detalles .label {
            display: inline-block;
            min-width: 100px;
            font-weight: bold;
        }

        .single_variation {
            margin-bottom: 0px !important;
        }
    </style>
    <script type="text/javascript">
        jQuery(function($) {
            function formatoEuropeo(numero) {
                return numero.toLocaleString('es-ES', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
            $('form.variations_form').on('found_variation', function(event, variation) {
                // Remover si ya existe
                $('.precio-sin-iva-detalles').remove();

                // Cálculos
                var precioConIva = parseFloat(variation.display_price);
                var precioSinIva = precioConIva / 1.21;
                var iva = precioConIva - precioSinIva;

                // Crear HTML
                var html = '<div class="precio-sin-iva-detalles">' +
                    '<div><span class="label">Precio sin impuestos: $ </span> ' + formatoEuropeo(precioSinIva) + '</div>';

                setTimeout(function() {
                    // Insertar después del precio
                    $('.woocommerce-variation-price').after(html);
                }, 500);
            });

            $('form.variations_form').on('hide_variation', function() {
                $('.precio-sin-iva-detalles').remove();
            });
        });
    </script>
<?php
}

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
