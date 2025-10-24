<?php
// Agregar estas funciones al functions.php de tu theme child

// Solución 1: Forzar recálculo de shipping al aplicar cupón
add_action('woocommerce_applied_coupon', 'force_checkout_update_on_coupon');
add_action('woocommerce_removed_coupon', 'force_checkout_update_on_coupon');

function force_checkout_update_on_coupon($coupon_code)
{
    if (is_admin()) return;

    // Recalcular totales del carrito
    WC()->cart->calculate_totals();

    // Forzar actualización de los métodos de envío
    WC()->session->set('chosen_shipping_methods', array());
    WC()->cart->calculate_shipping();
}

// Solución 2: Script específico para Woostify theme
add_action('wp_footer', 'woostify_checkout_coupon_update_script');
function woostify_checkout_coupon_update_script()
{
    if (is_checkout() && !is_order_received_page()) {
?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Específico para Woostify theme
                $(document.body).on('applied_coupon removed_coupon', function() {
                    // Actualizar checkout
                    $('body').trigger('update_checkout');

                    // Forzar recálculo de shipping específicamente
                    setTimeout(function() {
                        $('.woocommerce-shipping-methods').closest('.woocommerce-checkout-review-order-table').trigger('update_checkout');
                    }, 500);
                });
            });
        </script>
<?php
    }
}

// Solución 3: Hook para recalcular envío automáticamente
add_filter('woocommerce_shipping_packages', 'recalculate_shipping_on_coupon_change');
function recalculate_shipping_on_coupon_change($packages)
{
    // Si hay cupones aplicados, forzar recálculo
    if (!empty(WC()->cart->get_applied_coupons())) {
        // Limpiar cache de shipping
        WC()->session->set('shipping_for_package_0', '');
    }
    return $packages;
}

// Solución 4: Ajax para actualización inmediata
add_action('wp_ajax_update_checkout_on_coupon', 'ajax_update_checkout_on_coupon');
add_action('wp_ajax_nopriv_update_checkout_on_coupon', 'ajax_update_checkout_on_coupon');

function ajax_update_checkout_on_coupon()
{
    // Recalcular todo
    WC()->cart->calculate_totals();
    WC()->cart->calculate_shipping();

    // Obtener fragmentos actualizados
    ob_start();
    woocommerce_order_review();
    $order_review = ob_get_clean();

    wp_send_json_success(array(
        'fragments' => array(
            '.woocommerce-checkout-review-order-table' => $order_review
        )
    ));
}

// //Cambiar email desde el que se envían los correos
// function example_from_email($email)
// {
//     return 'tienda@esencias.test';
// } // end example_from_email

// add_filter('wp_mail_from', 'example_from_email');

// //Cambiar nombre desde el que se envían los correos
// function example_from_name($name)
// {
//     return 'Helo Fragancias';
// } // end example_from_name
// add_filter('wp_mail_from_name', 'example_from_name');
