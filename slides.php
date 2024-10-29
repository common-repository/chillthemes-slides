<?php

/*
Plugin Name: ChillThemes Slides
Plugin URI: http://wordpress.org/plugins/chillthemes-slides
Description: Enables a slides post type for use in any of our Chill Themes.
Version: 1.0
Author: ChillThemes
Author URI: http://chillthemes.net
Author Email: itismattadams@gmail.com
License:

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/* Setup the plugin. */
add_action( 'plugins_loaded', 'chillthemes_slides_setup' );

/* Register plugin activation hook. */
register_activation_hook( __FILE__, 'chillthemes_slides_activation' );
	
/* Register plugin activation hook. */
register_deactivation_hook( __FILE__, 'chillthemes_slides_deactivation' );

/* Plugin setup function. */
function chillthemes_slides_setup() {

	/* Define the plugin version. */
	define( 'CHILLTHEMES_SLIDES_VER', '1.0' );

	/* Get the plugin directory URI. */
	define( 'CHILLTHEMES_SLIDES_URI', plugin_dir_url( __FILE__ ) );

	/* Load translations on the backend. */
	if ( is_admin() )
		load_plugin_textdomain( 'chillthemes-slides', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/* Register the custom post type. */
	add_action( 'init', 'chillthemes_register_slides' );

	/* Filter the post type columns. */
	add_filter( 'manage_edit-slides_columns', 'chillthemes_slides_columns' );

	/* Add the post type column. */
	add_action( 'manage_posts_custom_column', 'chillthemes_slides_column' );

}

/* Do things on plugin activation. */
function chillthemes_slides_activation() {
	flush_rewrite_rules();
}

/* Do things on plugin deactivation. */
function chillthemes_slides_deactivation() {
	flush_rewrite_rules();
}

/* Register the post type. */
function chillthemes_register_slides() {

	/* Set the post type labels. */
	$slides_labels = array(
		'name'					=> __( 'Slides', 'ChillThemes' ),
		'singular_name'			=> __( 'Slide Item', 'ChillThemes' ),
		'all_items'				=> __( 'Slide Items', 'ChillThemes' ),
		'add_new_item'			=> __( 'Add New Slide', 'ChillThemes' ),
		'edit_item'				=> __( 'Edit Slide', 'ChillThemes' ),
		'new_item'				=> __( 'New Slide', 'ChillThemes' ),
		'view_item'				=> __( 'View Slides', 'ChillThemes' ),
		'search_items'			=> __( 'Search Slides', 'ChillThemes' ),
		'not_found'				=> __( 'No slides found', 'ChillThemes' ),
		'not_found_in_trash'	=> __( 'No slides in trash', 'ChillThemes' )
	);

	/* Define the post type arguments. */
	$slides_args = array(
		'can_export'		=> true,
		'capability_type'	=> 'post',
		'has_archive'		=> true,
		'labels'			=> $slides_labels,
		'menu_icon'			=> CHILLTHEMES_SLIDES_URI . '/images/menu-icon.png',
		'public'			=> true,
		'query_var'			=> 'slide',
		'rewrite'			=> array( 'slug' => 'slides', 'with_front' => false ),
		'supports'			=> array( 'editor', 'thumbnail', 'title' )
	);

	/* Register the post type. */
	register_post_type( apply_filters( 'chillthemes_slides', 'slides' ), apply_filters( 'chillthemes_slides_args', $slides_args ) );

}

/* Filter the columns on the custom post type admin screen. */
function chillthemes_slides_columns( $columns ) {
	$columns = array(
		'cb'							=> '<input type="checkbox" />',
		'title'							=> __( 'Slide Title', 'ChillThemes' ),
		'chillthemes-slides-image'		=> __( 'Slide Image', 'ChillThemes' )
	);
	return $columns;
}

/* Filter the data on the custom post type admin screen. */
function chillthemes_slides_column( $column ) {
	switch( $column ) {

		/* If displaying the 'Image' column. */
		case 'chillthemes-slides-image' :
			$return = '<img src="' . the_post_thumbnail( array( 40, 40 ) ) . '" alt="' . get_the_title() . '" />';
		break;

		/* Just break out of the switch statement for everything else. */
		default : break;
	}
}

/* Sort the order of the posts using AJAX. */
function chillthemes_slides_sorting_page() {
	$chillthemes_slides_sort = add_submenu_page( 'edit.php?post_type=slides', __( 'Sort Slides', 'ChillThemes' ), __( 'Sort', 'ChillThemes' ), 'edit_posts', basename( __FILE__ ), 'chillthemes_slides_post_sorting_interface' );

	add_action( 'admin_print_scripts-' . $chillthemes_slides_sort, 'chillthemes_slides_scripts' );
	add_action( 'admin_print_styles-' . $chillthemes_slides_sort, 'chillthemes_slides_styles' );
}
add_action( 'admin_menu', 'chillthemes_slides_sorting_page' );

/* Create the AJAX sorting interface. */
function chillthemes_slides_post_sorting_interface() {
   $slides = new WP_Query(
    	array(
    		'orderby' => 'menu_order',
    		'order' => 'ASC',
    		'posts_per_page' => -1,
    		'post_type' => 'slides'
    	)
    );
?>

	<div class="wrap">

		<?php screen_icon( 'tools' ); ?>

		<h2><?php _e( 'Sort Slides', 'ChillThemes' ); ?></h2>

		<p><?php _e( 'Drag and drop the items into the order in which you want them to display.', 'ChillThemes' ); ?></p>			

		<ul id="chillthemes-slides-list">

			<?php while ( $slides->have_posts() ) : $slides->the_post(); if ( get_post_status() == 'publish' ) : ?>

				<li id="<?php the_id(); ?>" class="menu-item">

					<dl class="menu-item-bar">

						<dt class="menu-item-handle">
							<span class="menu-item-title"><?php the_title(); ?></span>
						</dt><!-- .menu-item-handle -->

					</dl><!-- .menu-item-bar -->

					<ul class="menu-item-transport"></ul>

				</li><!-- .menu-item -->

			<?php endif; endwhile; wp_reset_postdata(); ?>

		</ul><!-- #chillthemes-slides-list -->

	</div><!-- .wrap -->

<?php }

/* Save the order of the items when it is modified. */
function chillthemes_slides_save_sorted_order() {
	global $wpdb;

	$order = explode( ',', $_POST['order'] );
	$counter = 0;

	foreach( $order as $slides_id ) {
		$wpdb->update( $wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $slides_id ) );
		$counter++;
	}
	die(1);
}
add_action( 'wp_ajax_slides_sort', 'chillthemes_slides_save_sorted_order' );

/* Load the scripts required for the AJAX sorting. */
function chillthemes_slides_scripts() {
	wp_enqueue_script( 'jquery-ui-sortable' );
 	wp_enqueue_script( 'chillthemes-slides-sorting', CHILLTHEMES_SLIDES_URI . '/js/sort.js' );
}

/* Load the styles required for the AJAX sorting. */
function chillthemes_slides_styles() {
	wp_enqueue_style( 'nav-menu' );
}

?>