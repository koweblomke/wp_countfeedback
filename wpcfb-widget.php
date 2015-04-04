<?php
/**
 * WPPB Widget
 * allows the progress bar to be added to a configurable widget
 * @author René Visser
 * @since 1.0.0
 * @uses WP_Widget
 */

// Register and load the widget
function feedbackcount_load_widget() {
  register_widget( 'wpcfb_widget' );
}
add_action( 'widgets_init', 'feedbackcount_load_widget' );

// Creating the widget 
class wpcfb_widget extends WP_Widget {

  public function __construct() {
    $widget_options = array( 'classname' => 'wpcfb-widget', 'description' => __('Teller voor opgaven.', 'wpcfb-widget_domain' ) );
    $control_options = array( 'id_base' => 'wpcfb-widget' );
    $this->WP_Widget( 'wpcfb-widget', __('Opgaven teller Widget', 'wpcfb-widget_domain' ), $widget_options, $control_options );
  }

  // Creating widget front-end
  // This is where the action happens
  public function widget( $args, $instance ) {
    extract($args);

    if ( isset( $instance['title'] ) ) { $title = apply_filters( 'widget_title', $instance['title'] ); } else { $title = ''; }
    if ( isset( $instance[ 'maxopgavenj' ] ) ) { $maxopgavenj = $instance[ 'maxopgavenj' ]; } else { $maxopgavenj = '60'; }
    if ( isset( $instance[ 'maxopgavens' ] ) ) { $maxopgavens = $instance[ 'maxopgavens' ]; } else { $maxopgavens = '60'; }
    if ( isset( $instance[ 'colorj' ] ) ) { $colorj = $instance[ 'colorj' ]; } else { $colorj = ''; }
    if ( isset( $instance[ 'colors' ] ) ) { $colors = $instance[ 'colors' ]; } else { $colors = ''; }
    if ( isset( $instance[ 'candystripej' ] ) ) { $candystripej = $instance[ 'candystripej' ]; } else { $candystripej = false; }
    if ( isset( $instance[ 'candystripes' ] ) ) { $candystripes = $instance[ 'candystripes' ]; } else { $candystripes = false; }
    if ( isset( $instance[ 'listidj' ] ) ) { $listidj = $instance[ 'listidj' ]; } else { $listidj = 58; }
    if ( isset( $instance[ 'listids' ] ) ) { $listids = $instance[ 'listids' ]; } else { $listids = 45; }
    if ( isset( $instance[ 'mcapikey' ] ) ) { $mcapikey = $instance[ 'mcapikey' ]; } else { $mcapikey = ''; }

    // before and after widget arguments are defined by themes
    echo $args['before_widget'];
    if ( ! empty( $title ) )
      echo $args['before_title'] . $title . $args['after_title'];

    $location = 'inside';

    $lists = mc4wp_call($mcapikey, 'lists/list');
    foreach ($lists->data as $list) {
      if($list->id == $listidj) {
        $countj = $list->stats->member_count + $list->stats->unsubscribe_count;
      }
      if($list->id == $listids) {
        $counts = $list->stats->member_count + $list->stats->unsubscribe_count;
      }
    }


    $option = null;
    if ( $colorj ) $option .= $colorj;
    if ( $candystripej ) $option .= ' ' . $candystripej;
    $wpcfb_check_results = check_pos($countj.'/'.$maxopgavenj); 
    $percent = $wpcfb_check_results[0];
    if ( $countj > $maxopgavenj ) $width = 100;
    else $width = $wpcfb_check_results[1];
    $the_progress_bar_j = get_progress_bar($location, $countj.'/'.$maxopgavenj, $progress, $option, $width, 'true');

    $option = null;
    if ( $colors ) $option .= $colors;
    if ( $candystripes ) $option .= ' ' . $candystripes;
    $wpcfb_check_results = check_pos($counts.'/'.$maxopgavens); 
    $percent = $wpcfb_check_results[0];
    if ( $counts > $maxopgavens ) $width = 100;
    else $width = $wpcfb_check_results[1];
    $the_progress_bar_s = get_progress_bar($location, $counts.'/'.$maxopgavens, $progress, $option, $width, 'true');

    // This is where you run the code and display the output
    echo '<h5>Aantal opgaven bij junioren:</h5>';
    echo $the_progress_bar_j;
    echo '<h5>Aantal opgaven bij senioren:</h5>';
    echo $the_progress_bar_s;
    echo $args['after_widget'];
  }

  // Widget Backend 
  public function form( $instance ) {

    if ( isset( $instance[ 'title' ] ) ) { $title = $instance[ 'title' ]; } else { $title = __( 'Opgave teller', 'wpcfb-widget_domain' );}
    if ( isset( $instance[ 'maxopgavenj' ] ) ) { $maxopgavenj = $instance[ 'maxopgavenj' ]; } else { $maxopgavenj = 60; }
    if ( isset( $instance[ 'maxopgavens' ] ) ) { $maxopgavens = $instance[ 'maxopgavens' ]; } else { $maxopgavens = 60; }
    if ( isset( $instance['colorj'] ) ) { $colorj = $instance['colorj']; } else { $colorj = ''; } // a dropdown
    if ( isset( $instance['colors'] ) ) { $colors = $instance['colors']; } else { $colors = ''; } // a dropdown
    if ( isset( $instance['candystripej'] ) ) { $candystripej = $instance['candystripej']; } else { $candystripej = false; } // a radio button
    if ( isset( $instance['candystripes'] ) ) { $candystripes = $instance['candystripes']; } else { $candystripes = false; } // a radio button
    if ( isset( $instance[ 'listidj' ] ) ) { $listidj = $instance[ 'listidj' ]; } else { $listidj = 58; }
    if ( isset( $instance[ 'listids' ] ) ) { $listids = $instance[ 'listids' ]; } else { $listids = 45; }
    if ( isset( $instance[ 'mcapikey' ] ) ) { $mcapikey = $instance[ 'mcapikey' ]; } else { $mcapikey = ''; }

    // Widget admin form
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Titel:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'maxopgavenj' ); ?>"><?php _e( 'Max aantal opgaven junioren:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'maxopgavenj' ); ?>" name="<?php echo $this->get_field_name( 'maxopgavenj' ); ?>" type="text" value="<?php echo esc_attr( $maxopgavenj); ?>" />
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'maxopgavens' ); ?>"><?php _e( 'Max aantal opgaven senioren:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'maxopgavens' ); ?>" name="<?php echo $this->get_field_name( 'maxopgavens' ); ?>" type="text" value="<?php echo esc_attr( $maxopgavens); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_name('colorj'); ?>"><strong><?php _e( 'Kleur balk junioren', 'wpcfb-widget_domain' ); ?></strong></label>
      <select name="<?php echo $this->get_field_name('colorj'); ?>" id="<?php echo $this->get_field_id('colorj'); ?>" class="widefat">
        <?php
        $colorts = array(
          'red' => array(
            'name' => __( 'Rood', 'wpcfb-widget_domain' ),
            'value' => 'red'
          ),
          'blue' => array(
            'name' => __( 'Blauw', 'wpcfb-widget_domain' ),
            'value' => ''
          ),
          'green' => array(
            'name' => __( 'Groen', 'wpcfb-widget_domain' ),
            'value' => 'green'
          ),
          'orange' => array(
            'name' => __( 'Oranje', 'wpcfb-widget_domain' ),
            'value' => 'orange'
          ),
          'yellow' => array(
            'name' => __( 'Geel', 'wpcfb-widget_domain' ),
            'value' => 'yellow'
          )
        );
        foreach ( $colorts as $hue ) {
          echo '<option value="' . $hue['value'] . '" id="' . $hue['value'] . '"', $colorj == $hue['value'] ? ' selected="selected"' : '', '>', $hue['name'], '</option>';
        }
        ?>
      </select>
    </p>
    <p>
      <label for="<?php echo $this->get_field_name('colors'); ?>"><strong><?php _e( 'Kleur balk senioren', 'wpcfb-widget_domain' ); ?></strong></label>
      <select name="<?php echo $this->get_field_name('colors'); ?>" id="<?php echo $this->get_field_id('colors'); ?>" class="widefat">
        <?php
        $colorts = array(
          'red' => array(
            'name' => __( 'Rood', 'wpcfb-widget_domain' ),
            'value' => 'red'
          ),
          'blue' => array(
            'name' => __( 'Blauw', 'wpcfb-widget_domain' ),
            'value' => ''
          ),
          'green' => array(
            'name' => __( 'Groen', 'wpcfb-widget_domain' ),
            'value' => 'green'
          ),
          'orange' => array(
            'name' => __( 'Oranje', 'wpcfb-widget_domain' ),
            'value' => 'orange'
          ),
          'yellow' => array(
            'name' => __( 'Geel', 'wpcfb-widget_domain' ),
            'value' => 'yellow'
          )
        );
        foreach ( $colorts as $hue ) {
          echo '<option value="' . $hue['value'] . '" id="' . $hue['value'] . '"', $colors == $hue['value'] ? ' selected="selected"' : '', '>', $hue['name'], '</option>';
        }
        ?>
      </select>
    </p>
    <p>
      <label for="<?php echo $this->get_field_name('candystripej'); ?>"><strong><?php _e( 'Zuurstok junioren', 'wpcfb-widget_domain' ); ?></strong></label>
      <fieldset>
      <label for="<?php echo $this->get_field_name('candystripej'); ?>"><input type="radio" id="<?php echo $this->get_field_id('candystripej'); ?>" name="<?php echo $this->get_field_name('candystripej'); ?>" value="candystripe" <?php checked('candystripe', $candystripej); ?> /> <?php _e( 'Zuurstok', 'wpcfb-widget_domain' ); ?></label><br />
      <label for="<?php echo $this->get_field_name('animated-candystripej'); ?>"><input type="radio" id="<?php echo $this->get_field_id('candystripej'); ?>" name="<?php echo $this->get_field_name('candystripej'); ?>" value="animated-candystripe" <?php checked('animated-candystripe', $candystripej); ?> /> <?php _e( 'Draaiende zuurstok', 'wpcfb-widget_domain' ); ?></label><br />
      <label for="<?php echo $this->get_field_name('nonej'); ?>"><input type="radio" id="<?php echo $this->get_field_id('candystripej'); ?>" name="<?php echo $this->get_field_name('candystripej'); ?>" value="none" <?php checked('none', $candystripej); ?> /> <?php _e( 'Geen', 'wpcfb-widget_domain' ); ?></label><br />
      </fieldset><br />
      <span class="description"><?php _e( 'Wel of geen zuurstok voor junioren.', 'wpcfb-widget_domain' ); ?></span>
    </p>
    <p>
      <label for="<?php echo $this->get_field_name('candystripes'); ?>"><strong><?php _e( 'Zuurstok senioren', 'wpcfb-widget_domain' ); ?></strong></label>
      <fieldset>
      <label for="<?php echo $this->get_field_name('candystripes'); ?>"><input type="radio" id="<?php echo $this->get_field_id('candystripes'); ?>" name="<?php echo $this->get_field_name('candystripes'); ?>" value="candystripe" <?php checked('candystripe', $candystripes); ?> /> <?php _e( 'Zuurstok', 'wpcfb-widget_domain' ); ?></label><br />
      <label for="<?php echo $this->get_field_name('animated-candystripes'); ?>"><input type="radio" id="<?php echo $this->get_field_id('candystripes'); ?>" name="<?php echo $this->get_field_name('candystripes'); ?>" value="animated-candystripe" <?php checked('animated-candystripe', $candystripes); ?> /> <?php _e( 'Draaiende zuurstok', 'wpcfb-widget_domain' ); ?></label><br />
      <label for="<?php echo $this->get_field_name('nones'); ?>"><input type="radio" id="<?php echo $this->get_field_id('candystripes'); ?>" name="<?php echo $this->get_field_name('candystripes'); ?>" value="none" <?php checked('none', $candystripes); ?> /> <?php _e( 'Geen', 'wpcfb-widget_domain' ); ?></label><br />
      </fieldset><br />
      <span class="description"><?php _e( 'Wel of geen zuurstok voor senioren.', 'wpcfb-widget_domain' ); ?></span>
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'listidj' ); ?>"><?php _e( 'Mailchimp List ID junioren:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'listidj' ); ?>" name="<?php echo $this->get_field_name( 'listidj' ); ?>" type="text" value="<?php echo esc_attr( $listidj); ?>" />
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'listids' ); ?>"><?php _e( 'Mailchimp List ID senioren:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'listids' ); ?>" name="<?php echo $this->get_field_name( 'listids' ); ?>" type="text" value="<?php echo esc_attr( $listids); ?>" />
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'mcapikey' ); ?>"><?php _e( 'Mailchimp Api Key:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'mcapikey' ); ?>" name="<?php echo $this->get_field_name( 'mcapikey' ); ?>" type="text" value="<?php echo esc_attr( $mcapikey); ?>" />
    </p>
    <?php 
  }
    
  // Updating widget replacing old instances with new
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['maxopgavenj'] = ( ! empty( $new_instance['maxopgavenj'] ) ) ? strip_tags( $new_instance['maxopgavenj'] ) : '';
    $instance['maxopgavens'] = ( ! empty( $new_instance['maxopgavens'] ) ) ? strip_tags( $new_instance['maxopgavens'] ) : '';
    $instance['colorj'] = ( ! empty( $new_instance['colorj'] ) ) ? strip_tags( $new_instance['colorj'] ) : '';
    $instance['colors'] = ( ! empty( $new_instance['colors'] ) ) ? strip_tags( $new_instance['colors'] ) : '';
    $instance['candystripej'] = ( ! empty( $new_instance['candystripej'] ) ) ? strip_tags( $new_instance['candystripej'] ) : '';
    $instance['candystripes'] = ( ! empty( $new_instance['candystripes'] ) ) ? strip_tags( $new_instance['candystripes'] ) : '';
    $instance[ 'listidj' ] = ( ! empty( $new_instance[ 'listidj' ] ) ) ? strip_tags( $new_instance['listidj'] ) : '';
    $instance[ 'listids' ] = ( ! empty( $new_instance[ 'listids' ] ) ) ? strip_tags( $new_instance['listids'] ) : '';
    $instance[ 'mcapikey' ] = ( ! empty( $new_instance[ 'mcapikey' ] ) ) ? strip_tags( $new_instance['mcapikey'] ) : '';
    return $instance;
  }
} // Class wpcfb-widget ends here
?>