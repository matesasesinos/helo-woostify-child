<form method="post" id="clientCodeForm">
    <h5>Ingresa el código de cliente</h5>
    <p class="cart-message" id="cart-message"></p>
    <div class="hl-inline-form">
        <input type="password" class="hl-input-text" placeholder="Código de cliente" name="client_code" id="client_code" value="">
        <p>
            <button type="submit" class="hl-input-button" id="client_code_submit">Enviar</button>
        </p>
    </div>
    <?php wp_nonce_field('client_code_nonce_action', 'client_code_nonce_field'); ?>
    <input type="hidden" name="client_id" value="<?php echo get_current_user_id() ?>" id="client_id">
</form>
<?php

add_action('wp_footer', function () {
?>
    <script>
        jQuery(document).ready(function() {
            jQuery('#clientCodeForm').on('submit', function(e) {
                e.preventDefault()
                console.log('first')
                var data = {
                    client_code_nonce_field: jQuery('#client_code_nonce_field').val(),
                    client_code: jQuery('#client_code').val(),
                    client_id: jQuery('#client_id').val(),
                    action: 'client_special_code'
                };

                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    data: data,
                    success: function(response) {
                        console.log(response)
                        if (response.success) {
                            window.location.reload();
                            return;
                        }

                        jQuery('#cart-message').html(response.data).addClass('error').css('display', 'flex');

                        setTimeout(() => {
                            jQuery('#cart-message').html('').fadeOut('slow', function() {
                                jQuery(this).removeClass('error')
                            })
                        }, 4000)
                    },
                    error: function(error) {
                        console.log(error)
                        jQuery('#cart-message').html(error.data).addClass('error').css('display', 'flex');
                        setTimeout(() => {
                            jQuery('#cart-message').html('Ocurrio un error').fadeOut('slow', function() {
                                jQuery(this).removeClass('error')
                            })
                        }, 4000)
                    }
                })
            })
        })
    </script>
<?php
});
