<?php

if (!function_exists('heloUpdateCustomerShippingAddress')) {
    function heloUpdateCustomerShippingAddress()
    {
        if (!isset($_POST['address1']) || !isset($_POST['city']) || !isset($_POST['zip']) || !isset($_POST['state'])) {
            echo wp_send_json_error('Todos los campos son obligatorios', 401);
            wp_die();
        }

        if (!isset($_POST['order'])) {
            echo wp_send_json_error('Ocurrio un error con la orden', 403);
            wp_die();
        }

        $order = wc_get_order($_POST['order']);
       
        if (!$order) {
            echo wp_send_json_error('Orden no encontrada', 404);
            wp_die();
        }

        $order->set_shipping_address_1($_POST['address1']);
        $order->set_shipping_postcode($_POST['zip']);
        $order->set_shipping_city($_POST['city']);
        $order->set_shipping_state($_POST['state']);

        if (!empty($_POST['address2'])) {
            $order->set_shipping_address_2($_POST['address2']);
        } else {
            $order->set_shipping_address_2('');
        }

        $order->save();
        echo wp_send_json_success('Dirección actualizada');
        wp_die();
    }

    add_action('wp_ajax_update_customer_address', 'heloUpdateCustomerShippingAddress');
    add_action('wp_ajax_nopriv_update_customer_address', 'heloUpdateCustomerShippingAddress');
}
