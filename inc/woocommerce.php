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
add_filter('woocommerce_account_menu_items', 'quitar_items_mi_cuenta');
function quitar_items_mi_cuenta($items)
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
