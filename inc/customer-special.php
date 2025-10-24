<?php

const HELO_CS_URL = 'clientes-especiales';

//hide category in shop list
if (!function_exists('hideCategoryInList')) {
    function hideCategoryInList()
    {
        $style = '';
        if (is_user_logged_in()) {
            $user = get_user_by('id', get_current_user_id());

            if (in_array('especial', $user->roles)) {
                $style = '<style>
                .wp-block-woocommerce-product-categories.wc-block-product-categories.is-list ul li:nth-child(2) {
                        display: none ! IMPORTANT;
                    }
            </style>';
            } else {
                $style = '';
            }
        } else {
            $style = '';
        }

        echo $style;
    }

    add_action('wp_head', 'hideCategoryInList');
}

if (!function_exists('redirectCategorySpecial')) {
    function redirectCategorySpecial()
    {
        if (is_product_category(HELO_CS_URL)) {
            $page = get_page_by_path(HELO_CS_URL);
            wp_redirect(get_permalink($page->ID));
            exit;
        }
    }

    add_action('template_redirect', 'redirectCategorySpecial');
}

//verify user in category
if (!function_exists('verifyUserInCategory')) {
    function verifyUserInCategory()
    {
        $page = get_post(get_queried_object_id());

        if ($page->post_name != HELO_CS_URL)
            return;

        if (!is_user_logged_in()) {
            $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : wc_get_page_permalink('myaccount');
            wp_redirect(wc_get_page_permalink('myaccount') . '?redirect_to=' . urlencode($redirect_url));
            exit;
        }

        $user = get_user_by('id', get_current_user_id());

        if (!in_array('especial', $user->roles) && !in_array('administrator', $user->roles)) {
            wc_add_notice('No tienes permiso para ver estos productos.', 'error');
            wp_redirect(wc_get_page_permalink('myaccount'));
            exit;
        }
    }

    add_action('template_redirect', 'verifyUserInCategory');
}

//hide field in admin
if (!function_exists('showFieldForAdmin')) {
    function showFieldForAdmin($field)
    {
        global $post;

        // Verifica si estamos editando un producto en el admin
        if ($post && get_post_type($post) === 'product') {
            $categoria_especifica = HELO_CS_URL; // Cambiar por el slug de la categoría

            // Obtiene las categorías del producto
            $categorias = wp_get_post_terms($post->ID, 'product_cat', array('fields' => 'slugs'));

            // Si el producto NO está en la categoría, oculta el campo
            if (!in_array($categoria_especifica, $categorias)) {
                return false; // Oculta el campo en ACF
            }
        }

        return $field;
    }
    add_filter('acf/prepare_field/name=clientes', 'showFieldForAdmin');
}

if (!function_exists('setClientCodeCookie')) {
    function setClientCodeCookie()
    {
        if (!isset($_POST['client_code_nonce_field'])) {
            echo wp_send_json_error('nonce missing', 401);
            wp_die();
        }


        if (!check_admin_referer('client_code_nonce_action', 'client_code_nonce_field')) {
            echo wp_send_json_error('bad nonce', 400);
            wp_die();
        }

        if (!isset($_POST['client_id'])) {
            echo wp_send_json_error('client id missing', 401);
            wp_die();
        }

        if (!isset($_POST['client_code']) || $_POST['client_code'] == '') {
            echo wp_send_json_error('El código de cliente es obligatorio', 200);
            wp_die();
        }

        $userCode = get_field('codigo_de_cliente', 'user_' . $_POST['client_id']);

        if ($_POST['client_code'] !== $userCode) {
            echo wp_send_json_error('El código de cliente no es correcto', 200);
            wp_die();
        }

        $clientCode = sanitize_text_field($_POST['client_code']);
        setcookie('wordpress_hl_client_code', $clientCode, time() + (30 * 24 * 60 * 60), '/'); // Vence en 30 días

        echo wp_send_json_success();
        wp_die();
    }

    add_action('wp_ajax_client_special_code', 'setClientCodeCookie');
    add_action('wp_ajax_nopriv_client_special_code', 'setClientCodeCookie');
}

if (!function_exists('setClientMenu')) {
    function setClientMenu($items, $args)
    {

        // Verificar si es el menú correcto (puedes cambiar 'primary' por el nombre de tu menú si es diferente)
        if ($args->theme_location == 'primary') {

            // Verificar si el usuario está logueado
            if (is_user_logged_in()) {
                $user = wp_get_current_user();

                // Verificar si tiene los roles 'cliente' y 'especial' o es un administrador
                if ((in_array('customer', $user->roles) && in_array('especial', $user->roles)) || in_array('administrator', $user->roles)) {
                    // Aquí agregamos el ítem de menú si se cumplen las condiciones
                    // Si deseas agregar un nuevo ítem de menú, lo puedes hacer así:
                    $new_item = '<li id="menu-item-51541" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-51541"><a href="' . esc_url(home_url('/clientes-especiales')) . '">
                    <span class="menu-item-text">Clientes especiales</span></a></a></li>';
                    $items .= $new_item;
                }
            }
        }

        return $items;
    }

    add_filter('wp_nav_menu_items', 'setClientMenu', 10, 2);
}


if (!function_exists('clientSpecialProducts')) {
    function clientSpecialProducts()
    {
        if (!is_user_logged_in())
            return [];

        $user = get_current_user_id();

        $productsQuery = wc_get_products([
            'type' => 'simple',
            'category' => [HELO_CS_URL]
        ]);

        $products = [];

        foreach ($productsQuery as $product) {
            $clientsAuth = get_field('clientes', $product->get_id());

            foreach ($clientsAuth as $client) {
                if ($client['seleccionar_cliente'] == $user) {
                    $products[] = [
                        'name' => $product->get_name(),
                        'image' => wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'single-post-thumbnail')[0],
                        'id' => $product->get_id(),
                        'price' => $client['precio_cliente'],
                        'min_qty' => $client['cantidad_minima_de_compra'] == 0 ? 1 : $client['cantidad_minima_de_compra']
                    ];
                }
            }
        }

        return $products;
    }

    add_action('template_redirect', 'clientSpecialProducts');
}

//add to cart
if (!function_exists('specialAddToCart')) {
    function specialAddToCart()
    {
        if (!isset($_POST['product_id']) && !isset($_POST['quantity']))
            return;

        $product = wc_get_product($_POST['product_id']);

        if (!$product) {
            echo wp_send_json_error('product not found', 404);
            wp_die();
        }

        $productId = intval($_POST['product_id']);
        $qty = intval($_POST['quantity']);

        $add = WC()->cart->add_to_cart($productId, $qty);

        echo wp_send_json_success($add);
        wp_die();
    }

    add_action('wp_ajax_add_special', 'specialAddToCart');
    add_action('wp_ajax_nopriv_add_special', 'specialAddToCart');
}

//change price
if (!function_exists('clientSpecialPriceCustom')) {
    function clientSpecialPriceCustom($cart_object)
    {

        $user = wp_get_current_user();

        if (!in_array('especial', $user->roles))
            return;

        foreach ($cart_object->cart_contents as $key => $value) {
            $productId = $value['data']->get_id();

            $clients =  get_field('clientes', $productId);

            foreach ($clients as $client) {
                if ($client['seleccionar_cliente'] == $user->ID) {
                    $price = $client['precio_cliente'];
                    $value['data']->set_price($price);
                }
            }
        }
    }

    add_action('woocommerce_before_calculate_totals', 'clientSpecialPriceCustom');
}
