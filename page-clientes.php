<?php

/**
 * Template name: Clientes especials
 */

get_header();

?>
<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
        do_action('woostify_page_before');

        if (!isset($_COOKIE['wordpress_hl_client_code'])) {
            get_template_part('parts/special/client', 'form');
        } else {
            get_template_part('parts/special/client', 'products', [
                'products' => clientSpecialProducts()
            ]);
        }
        /**
         * Functions hooked in to woostify_page_after action
         *
         * @hooked woostify_display_comments - 10
         */
        do_action('woostify_page_after');
        ?>
    </main>
</div>
<?php

get_footer();
