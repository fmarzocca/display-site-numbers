<?php
/*
  Plugin Name: Display Site Numbers
  Plugin URI: https://github.com/fmarzocca/display-site-numbers
  Description: A widget to display all relevant site content numbers
  Version: 1.2.1
  Author: Fabio Marzocca
  Author URI: http://www.marzocca.net
  Text Domain:   display-site-numbers
    Domain Path:   /languages/
  License: GPL2

  Copyright 2015-2024  by Fabio Marzocca  (email : marzoccafabio@gmail.com)

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


defined('ABSPATH') or die("No script kiddies please!");
define('DSN_DIR', dirname(__FILE__));
define('DSN_ATTACHMENT_COUNT_CACHE_KEY', 'display_site_numbers_attachment_count');
define('DSN_ATTACHMENT_COUNT_CACHE_GROUP', 'display_site_numbers');


class display_site_numbers extends WP_Widget {

  function __construct() {
    parent::__construct( 'display-site-numbers',
              'Display Site Numbers',
              array( 'description' => __('Display Site Numbers', 'display-site-numbers'),
                            'customize_selective_refresh' => true,)
               );

     if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'DSN_css' ) );
            }
  }

  /* Add CSS */
function DSN_css(){
    wp_register_style( 'DSN_css', plugins_url( 'DSN.css' , __FILE__ ) );
    wp_enqueue_style( 'DSN_css' );
  } // function




/******** Creating widget front-end ********************************/

public function widget( $args, $instance ) {
    $instance = wp_parse_args( (array) $instance, DSN_widget_defaults() );
    $title = apply_filters( 'widget_title', $instance['title'] );

    // before and after widget arguments are defined by themes
    echo wp_kses_post( $args['before_widget'] );
    if ( ! empty( $title ) )
        echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );

    // This is where you run the code and display the output
    $count_arr = DSN_counters();
    $counter_settings = array(
        'posts' => 'ck_posts',
        'pages' => 'ck_pages',
        'cats'  => 'ck_cats',
        'auth'  => 'ck_auth',
        'tags'  => 'ck_tags',
        'comm'  => 'ck_comm',
        'imgs'  => 'ck_imgs',
    );
    echo '<div class="DSN-wrapper"><ul>';
    foreach ($count_arr as $key => $count) {
        if ( isset( $counter_settings[ $key ] ) && empty( $instance[ $counter_settings[ $key ] ] ) ) {
            continue;
        }
        $label = ucwords(str_replace('_', ' ', $key)); // Converts 'custom_post' to 'Custom Post'
        DSN_dressit( $label, absint( $count ) );
    }
    echo "</ul></div>";
    echo wp_kses_post( $args['after_widget'] );
}


/********** Widget Backend **********************************/

  public function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, DSN_widget_defaults() );
    $title = $instance['title'];
    $ck_posts = $instance['ck_posts'];
    $ck_pages = $instance['ck_pages'];
    $ck_cats = $instance['ck_cats'];
    $ck_auth = $instance['ck_auth'];
    $ck_tags = $instance['ck_tags'];
    $ck_comm = $instance['ck_comm'];
    $ck_imgs = $instance['ck_imgs'];
    // Widget admin form
    ?>
<p>
<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'display-site-numbers'); ?></label>
<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<fieldset>
<legend class="description"><?php esc_html_e('Select what to display:', 'display-site-numbers'); ?></legend>
<ul>
<li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_posts' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_posts ); ?> />
<label class="choice" for="<?php echo esc_attr( $this->get_field_id( 'ck_posts' ) ); ?>"><?php esc_html_e('Posts count', 'display-site-numbers'); ?></label></li>
<li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_pages' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_pages' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_pages ); ?> />
<label class="choice" for="<?php echo esc_attr( $this->get_field_id( 'ck_pages' ) ); ?>"><?php esc_html_e('Pages count', 'display-site-numbers'); ?></label></li>
<li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_cats' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_cats' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_cats ); ?> />
<label class="choice" for="<?php echo esc_attr( $this->get_field_id( 'ck_cats' ) ); ?>"><?php esc_html_e('Categories count', 'display-site-numbers'); ?></label></li>
<li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_auth' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_auth' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_auth ); ?> />
<label class="choice" for="<?php echo esc_attr( $this->get_field_id( 'ck_auth' ) ); ?>"><?php esc_html_e('Authors count', 'display-site-numbers'); ?></label></li>
<li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_tags' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_tags' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_tags ); ?> />
<label class="choice" for="<?php echo esc_attr( $this->get_field_id( 'ck_tags' ) ); ?>"><?php esc_html_e('Tags count', 'display-site-numbers'); ?></label></li>
<li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_comm' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_comm' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_comm ); ?> />
<label class="choice" for="<?php echo esc_attr( $this->get_field_id( 'ck_comm' ) ); ?>"><?php esc_html_e('Comments count', 'display-site-numbers'); ?></label></li>
<li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_imgs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_imgs' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_imgs ); ?> />
<label class="choice" for="<?php echo esc_attr( $this->get_field_id( 'ck_imgs' ) ); ?>"><?php esc_html_e('Images count', 'display-site-numbers'); ?></label></li>
</ul>
</fieldset>
<?php
  }

/****** Updating widget replacing old instances with new *****************/

  public function update( $new_instance, $old_instance ) {
    $instance = wp_parse_args( (array) $old_instance, DSN_widget_defaults() );
    $new_instance = (array) $new_instance;
    $instance['title'] = ! empty( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
    $instance['ck_posts'] = ! empty( $new_instance['ck_posts'] ) ? 1 : 0;
    $instance['ck_pages'] = ! empty( $new_instance['ck_pages'] ) ? 1 : 0;
    $instance['ck_cats']  = ! empty( $new_instance['ck_cats'] ) ? 1 : 0;
    $instance['ck_auth']  = ! empty( $new_instance['ck_auth'] ) ? 1 : 0;
    $instance['ck_tags']  = ! empty( $new_instance['ck_tags'] ) ? 1 : 0;
    $instance['ck_comm']  = ! empty( $new_instance['ck_comm'] ) ? 1 : 0;
    $instance['ck_imgs']  = ! empty( $new_instance['ck_imgs'] ) ? 1 : 0;

    return $instance;
  }



} // Class display_site_numbers ends here

/**
 * Defaults for widget instances, including instances saved before checkbox
 * values were used by the front-end renderer.
 *
 * Missing values default to enabled so existing widgets do not lose counters
 * when they are upgraded. Explicitly unchecked values are stored as 0 by
 * update().
 */
function DSN_widget_defaults() {
    return array(
        'title'    => __( 'Total Site Numbers', 'display-site-numbers' ),
        'ck_posts' => 1,
        'ck_pages' => 1,
        'ck_cats'  => 1,
        'ck_auth'  => 1,
        'ck_tags'  => 1,
        'ck_comm'  => 1,
        'ck_imgs'  => 1,
    );
}

/********************** Utility Functions *********************************/

/************** Get the counters ********************/

function DSN_counters() {
    global $wpdb;
    $count_arr = array();
    // Standard posts count
    $post_counts = wp_count_posts();
    $count_arr['posts'] = ( is_object( $post_counts ) && isset( $post_counts->publish ) ) ? absint( $post_counts->publish ) : 0;
    // Standard pages count
    $page_counts = wp_count_posts( 'page' );
    $count_arr['pages'] = ( is_object( $page_counts ) && isset( $page_counts->publish ) ) ? absint( $page_counts->publish ) : 0;
    // Images count
    $attachment_count_found = false;
    $attachment_count = wp_cache_get( DSN_ATTACHMENT_COUNT_CACHE_KEY, DSN_ATTACHMENT_COUNT_CACHE_GROUP, false, $attachment_count_found );
    if ( $attachment_count_found ) {
        $count_arr['imgs'] = absint( $attachment_count );
    } else {
        $count_arr['imgs'] = absint( $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->prefix}posts WHERE post_type = 'attachment'" ) );
        wp_cache_set( DSN_ATTACHMENT_COUNT_CACHE_KEY, $count_arr['imgs'], DSN_ATTACHMENT_COUNT_CACHE_GROUP );
    }
    // Categories count
    $categories_count = wp_count_terms( 'category' );
    $count_arr['cats'] = is_wp_error( $categories_count ) ? 0 : absint( $categories_count );
    // Tags count
    $tags_count = wp_count_terms( 'post_tag' );
    $count_arr['tags'] = is_wp_error( $tags_count ) ? 0 : absint( $tags_count );
    // Comments count
    $comments_count = wp_count_comments();
    $count_arr['comm'] = ( is_object( $comments_count ) && isset( $comments_count->total_comments ) ) ? absint( $comments_count->total_comments ) : 0;
    // Authors count
    $users = count_users();
    $count_arr['auth'] = isset( $users['avail_roles']['author'] ) ? absint( $users['avail_roles']['author'] ) : 0;

    // Loop through all public custom post types
    $args = array(
       'public'   => true,
       '_builtin' => false // Exclude built-in types like post and page
    );
    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'

    $post_types = get_post_types($args, $output, $operator);

    foreach ($post_types as $post_type) {
        // Count each custom post type's published posts
        $post_type_counts = wp_count_posts( $post_type );
        $count_arr[ $post_type ] = ( is_object( $post_type_counts ) && isset( $post_type_counts->publish ) ) ? absint( $post_type_counts->publish ) : 0;
    }

    return $count_arr;
}

// Invalidate only this plugin's attachment count when the row set changes.
function DSN_clear_attachment_count_cache() {
    wp_cache_delete( DSN_ATTACHMENT_COUNT_CACHE_KEY, DSN_ATTACHMENT_COUNT_CACHE_GROUP );
}
add_action( 'add_attachment', 'DSN_clear_attachment_count_cache' );
add_action( 'delete_attachment', 'DSN_clear_attachment_count_cache' );


/*************** Dress the rows ****************/

  function DSN_dressit($item, $count) {
    echo "<li><span class='item'>" . esc_html( $item ) . ": </span><span class='count'>" . esc_html( $count ) . "</span></li>";

  }

/*************************************************/


// Register and load the widget
function DSN_load_widget() {
  register_widget( 'display_site_numbers' );
}
add_action( 'widgets_init', 'DSN_load_widget' );

/* Localization */

function DSN_load_i18n(){
  // load_plugin_textdomain disabled: WordPress.org handles translations automatically.
  }
  add_action( 'plugins_loaded', 'DSN_load_i18n' );




/************** Add shortcode **************/
function DSN_list($atts) {
    // Adding "Types" to the default 'show' attribute list to include custom post types by default
    $atts = shortcode_atts(array(
        'show' => "Categories, Posts, Pages, Images, Authors, Tags, Comments, Types"
    ), (array) $atts, 'DSN-list');

    // Enqueue only when the shortcode is actually rendered.
    wp_enqueue_style( 'DSN_css', plugins_url( 'DSN.css', __FILE__ ) );

    $valid_options = array(
        'authors'    => 'Authors',
        'categories' => 'Categories',
        'posts'      => 'Posts',
        'pages'      => 'Pages',
        'comments'   => 'Comments',
        'tags'       => 'Tags',
        'images'     => 'Images',
        'types'      => 'Types',
    );
    $show = is_string( $atts['show'] ) ? $atts['show'] : '';
    $requested_options = array();
    foreach ( explode( ',', $show ) as $option ) {
        $option = strtolower( trim( $option ) );
        if ( isset( $valid_options[ $option ] ) ) {
            $requested_options[ $valid_options[ $option ] ] = true;
        }
    }

    $count_arr = DSN_counters();
    ob_start();
    echo '<div class="DSN-wrapper"><ul>';

    // Display counts for standard selections
    if ( isset( $requested_options['Authors'] ) ) {
        DSN_dressit(__('Authors', 'display-site-numbers'), $count_arr['auth']);
    }
    if ( isset( $requested_options['Categories'] ) ) {
        DSN_dressit(__('Categories', 'display-site-numbers'), $count_arr['cats']);
    }
    if ( isset( $requested_options['Posts'] ) ) {
        DSN_dressit(__('Posts', 'display-site-numbers'), $count_arr['posts']);
    }
    if ( isset( $requested_options['Pages'] ) ) {
        DSN_dressit(__('Pages', 'display-site-numbers'), $count_arr['pages']);
    }
    if ( isset( $requested_options['Comments'] ) ) {
        DSN_dressit(__('Comments', 'display-site-numbers'), $count_arr['comm']);
    }
    if ( isset( $requested_options['Tags'] ) ) {
        DSN_dressit(__('Tags', 'display-site-numbers'), $count_arr['tags']);
    }
    if ( isset( $requested_options['Images'] ) ) {
        DSN_dressit(__('Images', 'display-site-numbers'), $count_arr['imgs']);
    }

    // Display counts for all custom post types as a group under "Types"
    if ( isset( $requested_options['Types'] ) ) {
        $post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
        foreach ($post_types as $post_type) {
            $label = $post_type->labels->name; // Get plural label of the post type
            $post_type_counts = wp_count_posts( $post_type->name );
            $count = ( is_object( $post_type_counts ) && isset( $post_type_counts->publish ) ) ? $post_type_counts->publish : 0;
            DSN_dressit( $label, absint( $count ) );
        }
    }

    echo "</ul></div>";
    echo '<p style="clear: both;"></p>';
    $output_string = ob_get_contents();
    ob_end_clean();
    return $output_string;
}
add_shortcode("DSN-list", "DSN_list");


?>
