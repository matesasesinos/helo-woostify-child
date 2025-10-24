<?php
/**
 * Template Name: Search Results
 *
 * @package woostify
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

	<?php
	// Capturar el término de búsqueda
	$search_query = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

	// Crear una nueva query SOLO para productos
	$args = array(
		'post_type'      => 'product',
		's'              => $search_query,
		'posts_per_page' => 12, // o los que quieras
		'paged'          => ( get_query_var('paged') ) ? get_query_var('paged') : 1,
	);
	$search = new WP_Query( $args );

	if ( $search->have_posts() ) : ?>

		<header class="page-header">
			<h1 class="page-title entry-title">
				<?php
					printf( esc_html__( 'Resultados de búsqueda para: %s', 'woostify' ), '<span>' . esc_html( $search_query ) . '</span>' );
				?>
			</h1>
		</header><!-- .page-header -->

		<?php
		while ( $search->have_posts() ) :
			$search->the_post();
			wc_get_template_part( 'content', 'product' ); // Mostrar productos
		endwhile;

		// Paginación si quieres
		the_posts_pagination();

	else :

		get_template_part( 'template-parts/content', 'none' );

	endif;

	wp_reset_postdata();
	?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
do_action( 'woostify_sidebar' );
get_footer();
