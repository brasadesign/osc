<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till #main div
 *
 * @package Odin
 * @since 2.2.0
 */
?>
<?php
// global $wp_query;
//  $wp_query->is_404 = false;

// 	$endereco=explode( "/",$_SERVER[REQUEST_URI]);
//  	$parte1 = $endereco[1];
//   	$parte2 = $endereco[2];
//   	$parte4 = $endereco[4];
//   	// print_r($endereco);
//   	// 	echo $parte1."nt:˜".is_integer((int)$parte1);

// 	if ($parte1 == "tag" ){
// 		// echo site_url( "?s=".$parte2 );
// 		header(	'Location: '.site_url( "?s=".$parte2 ));
		
// 	}
// 	// else if (is_integer((int)$parte1) AND is_integer((int)$parte2)) {
// 	// 	header('Location: '.site_url( "noticia".$parte4 ));
// 	// 	// echo site_url( "noticia/".$parte4 );
// 	// }
	?>

	<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php if ( ! get_option( 'site_icon' ) ) : ?>
		<link href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.ico" rel="shortcut icon" />
	<?php endif; ?>
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/assets/js/html5.js"></script>
	<![endif]-->
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<a id="skippy" class="sr-only sr-only-focusable" href="#content">
		<div class="container">
			<span class="skiplink-text"><?php _e( 'Skip to content', 'odin' ); ?></span>
		</div>
	</a>

	<header id="header" class="header-todos"role="banner">
		<div class="container row">
			<div id="logo"class='col-sm-4'>
				<a href="<?php echo get_home_url( ); ?>">
					<img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-osc.png" alt="">
				</a>
			</div>
			<div class='col-sm-8'>

				<div id="main-navigation" class=" col-sm-12 navbar navbar-default">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-navigation">
						<span class="sr-only"><?php _e( 'Toggle navigation', 'odin' ); ?></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						
					</div>
					<nav class="collapse navbar-collapse navbar-main-navigation" role="navigation">
						<?php
							wp_nav_menu(
								array(
									'theme_location' => 'main-menu',
									'depth'          => 2,
									'container'      => false,
									'menu_class'     => 'nav navbar-nav',
									'fallback_cb'    => 'Odin_Bootstrap_Nav_Walker::fallback',
									'walker'         => new Odin_Bootstrap_Nav_Walker()
								)
							);
						?>
						
					</nav><!-- .navbar-collapse -->
				</div><!-- #main-navigation-->
				<div class="col-sm-6 texto-header">
				<?php $odin_footer_opts = get_option( 'odin_general' );?>
				<a target='_blank'  href="<?php echo $odin_footer_opts['facebook'];?>"><img  class="social-footer" class='inline-block' src="<?php echo get_template_directory_uri(); ?>/assets/images/face-color.png"></a>
				<a target='_blank'  href="<?php echo $odin_footer_opts['twitter'];?>"><img class="social-footer"  class='inline-block' src="<?php echo get_template_directory_uri(); ?>/assets/images/twitter-color.png"></a>
				<a target='_blank'  href="<?php echo $odin_footer_opts['youtube'];?>"><img class="social-footer"  class='inline-block' src="<?php echo get_template_directory_uri(); ?>/assets/images/youtube-color.png"></a>
				</div>
				<form method="get" class="col-sm-6 navbar-form " action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
					<label for="navbar-search" class="sr-only">
						<?php _e( 'Search:', 'odin' ); ?>
					</label>
					<div class="form-group">
						<input type="search" value="<?php echo get_search_query(); ?>" class="form-control" name="s" id="navbar-search" />
					</div>
					<button type="submit" class="btn botao-busca"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/lupa.png" alt=""></button>
				</form>
			</div><!--col-sm-8-->
		</div><!-- .container-->
	</header><!-- #header -->

	<div id="wrapper" class="container">
