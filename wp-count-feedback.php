<?php
/*
Plugin Name: Count Feedback
Plugin URI: nota
Description: A simple plugin that provides a template function to count feedback, as well as filtering by parent post. The function can either return or display the result. <em>Based off Clint Howarth's count_posts</em>
Author: René Visser
Version: 1.0
Author URI: http://lodenhelrun.nl/
Licensed under the The GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
*/
include ( plugin_dir_path( __FILE__ ) . 'wpcfb-widget.php' );
include ( plugin_dir_path( __FILE__ ) . 'functions.php' );

function wpcfb_init() {
  $wppb_path = plugin_dir_url( __FILE__ );
  if ( !is_admin() ) { // don't load this if we're in the backend
    wp_register_style( 'wpcfb_css', $wppb_path . 'css/wpcfb.css' );
    wp_enqueue_style( 'wpcfb_css' );
    wp_enqueue_script( 'jquery' );
    wp_register_script( 'wppb_animate', $wppb_path . 'js/wpcfb_animate.js', 'jquery' );
    wp_enqueue_script ( 'wppb_animate' );
  }
}
add_action( 'init', 'wpcfb_init' );

?>