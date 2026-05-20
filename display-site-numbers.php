<?php
/*
  Plugin Name: Display Site Numbers
  Plugin URI: https://github.com/fmarzocca/display-site-numbers
  Description: A widget to display all relevant site content numbers
  Version: 1.2
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
    extract($args);
    $title = apply_filters( 'widget_title', $instance['title'] );

    // before and after widget arguments are defined by themes
    echo wp_kses_post( $args['before_widget'] );
    if ( ! empty( $title ) )
        echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );

    // This is where you run the code and display the output
    $count_arr = DSN_counters();
    echo '<div class="DSN-wrapper"><ul>';
    foreach ($count_arr as $key => $count) {
        $label = ucwords(str_replace('_', ' ', $key)); // Converts 'custom_post' to 'Custom Post'
        DSN_dressit( esc_html( $label ), absint( $count ) );
    }
    echo "</ul></div>";
    echo wp_kses_post( $args['after_widget'] );
}


/********** Widget Backend **********************************/

  public function form( $instance ) {
    if ($instance) {
      $title = $instance[ 'title' ];
      $ck_posts = $instance['ck_posts'];
      $ck_pages = $instance['ck_pages'];
      $ck_cats = $instance['ck_cats'];
      $ck_auth = $instance['ck_auth'];
      $ck_tags = $instance['ck_tags'];
      $ck_comm = $instance['ck_comm'];
      $ck_imgs = $instance['ck_imgs'];
    }
    else {
      $title = __( 'Total Site Numbers', 'display-site-numbers' );
      $ck_posts = "1";
      $ck_pages = "1";
      $ck_cats = "1";
      $ck_auth = "1";
      $ck_tags = "1";
      $ck_comm = "1";
      $ck_imgs = "1";
    }
    // Widget admin form
    ?>
<p>
<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'display-site-numbers'); ?></label>
<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<p>

<label class="description" for="element_1"><?php esc_html_e('Select what to display:', 'display-site-numbers'); ?></label>
    <span>
      <ul><li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_posts' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_posts ); ?> />
<label class="choice" ><?php esc_html_e('Posts count', 'display-site-numbers'); ?></label></li>
<li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_pages' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_pages' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_pages ); ?> />
<label class="choice" ><?php esc_html_e('Pages count', 'display-site-numbers'); ?></label></li>
<li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_cats' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_cats' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_cats ); ?> />
<label class="choice" ><?php esc_html_e('Categories count', 'display-site-numbers'); ?></label></li>
<li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_auth' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_auth' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_auth ); ?> />
<label class="choice" ><?php esc_html_e('Authors count', 'display-site-numbers'); ?></label></li>
<li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_tags' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_tags' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_tags ); ?> />
<label class="choice" ><?php esc_html_e('Tags count', 'display-site-numbers'); ?></label></li>
<li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_comm' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_comm' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_comm ); ?> />
<label class="choice" ><?php esc_html_e('Comments count', 'display-site-numbers'); ?></label></li>
<li><input id="<?php echo esc_attr( $this->get_field_id( 'ck_imgs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ck_imgs' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $ck_imgs ); ?> />
<label class="choice" ><?php esc_html_e('Images count', 'display-site-numbers'); ?></label></li></span>
</li></ul></p>
<?php
  }

/****** Updating widget replacing old instances with new *****************/

  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
     $instance['ck_posts'] = $new_instance['ck_posts'];
     $instance['ck_pages'] = $new_instance['ck_pages'];
     $instance['ck_cats'] = $new_instance['ck_cats'];
     $instance['ck_auth'] = $new_instance['ck_auth'];
     $instance['ck_tags'] = $new_instance['ck_tags'];
     $instance['ck_comm'] = $new_instance['ck_comm'];
     $instance['ck_imgs'] = $new_instance['ck_imgs'];

    return $instance;
  }



} // Class display_site_numbers ends here

/********************** Utility Functions *********************************/

/************** Get the counters ********************/

function DSN_counters() {
    global $wpdb;
    $count_arr = array();
    // Standard posts count
    $count_arr['posts'] = wp_count_posts()->publish;
    // Standard pages count
    $count_arr['pages'] = wp_count_posts('page')->publish;
    // Images count
    $count_arr['imgs'] = $wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->prefix}posts WHERE post_type = 'attachment'");
    // Categories count
    $count_arr['cats'] = wp_count_terms('category');
    // Tags count
    $count_arr['tags'] = wp_count_terms('post_tag');
    // Comments count
    $count_arr['comm'] = wp_count_comments()->total_comments;
    // Authors count
    $users = count_users();
    $count_arr['auth'] = $users['avail_roles']['author'];

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
        $count_arr[$post_type] = wp_count_posts($post_type)->publish;
    }

    return $count_arr;
}


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
    ), $atts);

    $count_arr = DSN_counters();
    ob_start();
    echo '<div class="DSN-wrapper"><ul>';

    // Display counts for standard selections
    if (strpos($atts['show'], "Authors") !== false) {
        DSN_dressit(__('Authors', 'display-site-numbers'), $count_arr['auth']);
    }
    if (strpos($atts['show'], "Categories") !== false) {
        DSN_dressit(__('Categories', 'display-site-numbers'), $count_arr['cats']);
    }
    if (strpos($atts['show'], "Posts") !== false) {
        DSN_dressit(__('Posts', 'display-site-numbers'), $count_arr['posts']);
    }
    if (strpos($atts['show'], "Pages") !== false) {
        DSN_dressit(__('Pages', 'display-site-numbers'), $count_arr['pages']);
    }
    if (strpos($atts['show'], "Comments") !== false) {
        DSN_dressit(__('Comments', 'display-site-numbers'), $count_arr['comm']);
    }
    if (strpos($atts['show'], "Tags") !== false) {
        DSN_dressit(__('Tags', 'display-site-numbers'), $count_arr['tags']);
    }
    if (strpos($atts['show'], "Images") !== false) {
        DSN_dressit(__('Images', 'display-site-numbers'), $count_arr['imgs']);
    }

    // Display counts for all custom post types as a group under "Types"
    if (strpos($atts['show'], "Types") !== false) {
        $post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
        foreach ($post_types as $post_type) {
            $label = $post_type->labels->name; // Get plural label of the post type
            $count = wp_count_posts($post_type->name)->publish;
            DSN_dressit( esc_html( $label ), absint( $count ) );
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
