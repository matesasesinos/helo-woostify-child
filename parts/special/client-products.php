<?php
$productsGET = $args['products'];
$productList = str_replace('"', "'", json_encode($productsGET, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES));
$user = get_user_by('id', get_current_user_id());
?>
<h3>Productos de <?php echo $user->first_name ?></h3>
<p class="cart-message" id="cart-message"></p>
<div x-data="cartTable">
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Producto</th>
                <th>Precio (ARS)</th>
                <th>Cantidad</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="product in products" :key="product.id">
                <tr>
                    <td><img :src="product.image" alt="" width="50"></td>
                    <td x-text="product.name"></td>
                    <td x-text="product.price"></td>
                    <td style="display:flex; justify-content:center">
                        <input type="number"
                            x-model="quantities[product.id]"
                            :min="product.min_qty || 1"
                            @input="quantities[product.id] = Math.max(product.min_qty || 1, $event.target.value)"
                            class="hl-input-text qty">
                    </td>
                    <td>
                        <button class="hl-input-button" @click="addToCart(product.id)">Añadir al carrito</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
</div>

<?php add_action('wp_footer', function () use ($productList) {
?>
    <script>
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        var wpProducts = <?php echo $productList; ?>;
        document.addEventListener("alpine:init", () => {
            Alpine.data("cartTable", () => ({
                products: [],
                quantities: {},

                init() {
                    this.products = window.wpProducts || []; // Cargar productos desde PHP
                    this.products.forEach(product => {
                        this.quantities[product.id] = product.min_qty || 1;
                    });
                },

                addToCart(productId) {
                    let quantity = this.quantities[productId];

                    let data = {
                        action: "add_special",
                        product_id: productId,
                        quantity: quantity
                    };

                    jQuery.post(ajaxurl, data, function(response) {
                        if(response.success) {
                            jQuery(document.body).trigger('wc_fragment_refresh');

                            jQuery('.cart-message').html('Producto agregado').addClass('success').css('display', 'flex')
                            setTimeout(() => {
                                jQuery('#cart-message').html('').fadeOut('slow', function() {
                                    jQuery(this).removeClass('success')
                                })
                            }, 1500)
                        }
                    });
                }
            }));
        });
    </script>

<?php
});
