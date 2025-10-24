<section class="woocommerce-order-details customer-confirm-address">
    <h2 class="woocommerce-order-details__title">Por favor, revisa tu dirección de envío</h2>
    <p>En caso de que algún dato no sea correcto, puedes corregirla en este formulario. En caso contrario no es necesario realizar ninguna acción.</p>
    <form method="post" id="confirm-address" class="woostify-contact-form">
        <div class="form-fields">
            <div>
                <label for="">Dirección</label>
                <input type="text"
                    placeholder="Dirección"
                    required
                    id="customer_address"
                    name="<?php echo $order->get_shipping_address_1() ? 'shipping_address_1' : 'billing_address_1' ?>"
                    value="<?php echo $order->get_shipping_address_1() ? $order->get_shipping_address_1() : $order->get_billing_address_1() ?>">
            </div>
            <?php if (!empty($order->get_shipping_address_2()) || !empty($order->get_billing_address_2())) : ?>
                <div>
                    <label for="">Dirección linea 2</label>
                    <input type="text"
                        placeholder="Dirección"
                        required
                        id="customer_address2"
                        name="<?php echo $order->get_shipping_address_2() ? 'shipping_address_2' : 'billing_address_2' ?>"
                        value="<?php echo $order->get_shipping_address_2() ? $order->get_shipping_address_2() : $order->get_billing_address_2() ?>">
                </div>
            <?php endif; ?>
            <div>
                <label for="">Ciudad</label>
                <input type="text"
                    placeholder="Ciudad"
                    required
                    id="customer_city"
                    name="<?php echo $order->get_shipping_city() ? 'shipping_city' : 'billing_city' ?>"
                    value="<?php echo $order->get_shipping_city() ? $order->get_shipping_city() : $order->get_billing_city() ?>">
            </div>
            <div>
                <label for="">Código postal</label>
                <input type="text" required
                    placeholder="Código postal"
                    id="customer_zip"
                    name="<?php echo $order->get_shipping_postcode() ? 'shipping_postcode' : 'billing_postcode' ?>"
                    value="<?php echo $order->get_shipping_postcode() ? $order->get_shipping_postcode() : $order->get_billing_postcode() ?>">
            </div>
            <div>
                <label for="">Provincia</label>
                <?php
                $shipping_country = $order->get_shipping_country() ?? $order->get_billing_country();
                $states = WC()->countries->get_states($shipping_country);
                $currentState = $order->get_shipping_state() ?? $order->get_billing_state();
                ?>
                <select name="<?php echo $order->get_shipping_state() ? 'shipping_state' : 'billing_state' ?>" required id="customer_state">
                    <option value="">-- seleccionar provincia --</option>
                    <?php foreach ($states as $key => $state): ?>
                        <option value="<?php echo esc_attr($key) ?>" <?php selected($key, $currentState, true) ?>>
                            <?php echo esc_html($state) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <p>
            <input type="hidden" id="customer_id" value="<?php echo $order->get_user_id() ?>">
            <input type="hidden" id="order_id" value="<?php echo $order->get_id() ?>">
            <button type="button" id="save-customer-address" class="button">Guardar dirección</button>
        </p>
    </form>
</section>