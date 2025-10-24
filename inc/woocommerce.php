<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Mostrar precio de variación solo si tiene precio establecido
add_filter('woocommerce_show_variation_price', 'filter_show_variation_price', 10, 3);
function filter_show_variation_price($condition, $product, $variation)
{
    if ($variation->get_price() === "") return false;
    else return true;
}

// Quitar el item de Descargas del menú de Mi Cuenta
add_filter('woocommerce_account_menu_items', 'remove_items_from_myaccount');
function remove_items_from_myaccount($items)
{
    unset($items['downloads']);      // Descargas
    // unset( $items['dashboard'] );   // Dashboard
    // unset( $items['orders'] );      // Pedidos
    // unset( $items['edit-address'] ); // Direcciones
    // unset( $items['payment-methods'] ); // Métodos de pago
    // unset( $items['edit-account'] );   // Detalles de cuenta
    // unset( $items['customer-logout'] ); // Cerrar sesión

    return $items;
}

// Busqueda en pagina de producto
add_action('woostify_theme_header', function () { ?>
    <?php if (is_product()): ?>
        <div id="search-container-product">
            <?php aws_get_search_form(true); ?>
        </div>
    <?php endif; ?>
<?php }, 999);

//Precios sin impuestos
add_action('wp_footer', 'show_price_without_iva_after_price');
function show_price_without_iva_after_price()
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
