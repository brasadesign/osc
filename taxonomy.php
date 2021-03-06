<?php
/**
 * The template for displaying Category pages.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Odin
 * @since 2.2.0
 */

get_header('internas'); ?>
	<h2 class="titulo">Noticias: <b> <?php echo $wp_query->get_queried_object()->name;?></b></h2>
	<main id="content" class="row" tabindex="-1" role="main">
			
			<?php
				// $term = get_query_var( );
							// print_r($wp_query->get_queried_object());

				$cat_home=$wp_query->get_queried_object()->term_id;
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1; //Aqui vem a página 1 por padrão 
				$args = array(
					'post_type' => 'noticia',
					'posts_per_page' =>12,
				    'paged' => $paged, 

						'tax_query' => array(
						array(
							'taxonomy' => 'category',
							'field'    => 'id',
							'terms'    =>$cat_home,

						),
					),
				);
	
				$WP_query_slider = new WP_Query( $args );
	
				if( $WP_query_slider->have_posts()  )
				{
				?>
					<div id="archive-<?php echo $cat_home;?> ">
				
			
					<?php
					while ( $WP_query_slider->have_posts() ) 
					{
						$WP_query_slider->the_post();
					
						get_template_part( 'content', 'noticia' );
					}
					echo odin_pagination( 2, 1, false, $WP_query_slider );

					?>
					</div>
					<?php

					wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly
				}
			 	?>
	</main><!-- #main -->

<?php
get_footer();
