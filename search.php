<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package Odin
 * @since 2.2.0
 */

get_header('banco'); 
$post_type = get_query_var('post_type'); 
$tema=$_GET["tema"];
$uf=$_GET["uf"];
$cidade=$_GET["cidade"];
$nome=$_GET["s"];
if( $post_type == 'pratica'){
$args = array(
	's'			=> $nome,
	'post_type' => 'pratica',
	'paged' =>get_query_var( 'paged' ),

);
if ($cidade != 0 && $uf !=''){
	$args['meta_query']=array(
		array(
			'key'     => 'uf',
			'value'   => $uf,
			'compare' => '=',
		),
		array(
			'key' => 'cidade',
			'value'   => $cidade,
			'compare' => '=',
		),
	);	
}
elseif($cidade!=0){
	$args['meta_query']=array(
		array(
			'key' => 'cidade',
			'value'   => $cidade,
			'compare' => '=',
		),
	);
}
else if ($uf!=""){
	$args['meta_query']=array(
		array(
			'key'     => 'uf',
			'value'   => $uf,
			'compare' => '=',
		),
	);
}
if ($tema !=0){
	$args['tax_query']=array(
		array(
			'taxonomy' => 'tema',
			'field'    => 'id',
			'terms'    => $tema,
		),
	);
}

$query = new WP_Query( $args );

?>

<main id="content" class="<?php echo odin_classes_page_sidebar(); ?>" tabindex="-1" role="main">
			<?php if ( $query->have_posts() ) : ?>

				<header class="page-header">
					<h1 class="page-title"><?php printf( __( 'Busca', 'odin' ), get_search_query() ); ?></h1>
				</header><!-- .page-header -->

					<?php

						// Start the Loop.
						while ( $query->have_posts() ) : $query->the_post();

							/*
							 * Include the post format-specific template for the content. If you want to
							 * use this in a child theme, then include a file called called content-___.php
							 * (where ___ is the post format) and that will be used instead.
							 */
							get_template_part( 'content', 'pratica-lista' );

						endwhile;

						// Post navigation.
						odin_paging_nav();

					else :
						// If no content, include the "No posts found" template.
						get_template_part( 'content', 'none' );

				endif;
			?>

	</main><!-- #main -->

<?php

// The Loop
if ( $the_query->have_posts() ) {
	echo '<ul>';
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		echo '<li>' . get_the_title() . '</li>';
	}
	echo '</ul>';
} else {
	// no posts found
}
/* Restore original Post Data */
wp_reset_postdata();


}
else{
	?>

	<main id="content" class="<?php echo odin_classes_page_sidebar(); ?>" tabindex="-1" role="main">
			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'odin' ), get_search_query() ); ?></h1>
				</header><!-- .page-header -->

					<?php
						// Start the Loop.
						while ( have_posts() ) : the_post();

							/*
							 * Include the post format-specific template for the content. If you want to
							 * use this in a child theme, then include a file called called content-___.php
							 * (where ___ is the post format) and that will be used instead.
							 */
							get_template_part( 'content', get_post_format() );

						endwhile;

						// Post navigation.
						odin_paging_nav();

					else :
						// If no content, include the "No posts found" template.
						get_template_part( 'content', 'none' );

				endif;
			?>

	</main><!-- #main -->
	<?php 
}

get_footer();
