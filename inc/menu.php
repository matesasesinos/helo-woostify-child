<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!function_exists('helo__wishlist_menu')) {
    function helo__wishlist_menu($items, $args)
    {
        if ($args->theme_location == 'primary') {
            if (is_user_logged_in()) :
                $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-51541 favoritos-menu"><a title="Favoritos" href="' . esc_url(home_url('/favoritos')) . '"><svg  xmlns="http://www.w3.org/2000/svg"  width="32"  height="32"  viewBox="0 0 32 32"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-heart"><path stroke="none" d="M0 0h32v32H0z" fill="none"/><path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" /></svg></a></li>';
            endif;
        }

        return $items;
    }
    add_action('wp_nav_menu_items', 'helo__wishlist_menu', 20, 2);
}
