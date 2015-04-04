<?php
/**
 * Count Feedback
 * counts all feedback posts from a certain post_parent
 */
function count_feedback ($post_parent = '')
{
  global $wpdb;

  // Construct Query
  $query = "SELECT COUNT(*) FROM $wpdb->posts WHERE (post_type = 'feedback') AND (post_status !=  'trash') ";
  if ($post_parent != '') {
    $query .= "AND (post_parent = $post_parent) ";
  }

  //Output
  $output = $wpdb->get_var($query);

  return $output;
}

function mc4wp_call( $api_key, $method, array $data = array() )
{

  $data['apikey'] = $api_key;
  $url = 'https://' . substr( $api_key, -3 ) . '.api.mailchimp.com/2.0/' . $method . '.json';

  $response = wp_remote_post( $url, array( 
    'body' => $data,
    'timeout' => 15,
    'headers' => array('Accept-Encoding' => ''),
    'sslverify' => false
    ) 
  ); 

  // test for wp errors
  if( is_wp_error( $response ) ) {
    // show error message to admins
    $this->show_error( "HTTP Error: " . $response->get_error_message() );
    return false;
  }

  // dirty fix for older WP versions
  if( $method === 'helper/ping' && is_array( $response ) && isset( $response['headers']['content-length'] ) && (int) $response['headers']['content-length'] === 44 ) {
    return (object) array(
      'msg' => "Everything's Chimpy!"
    );
  }
  
  $body = wp_remote_retrieve_body( $response );
  return json_decode( $body );
}

/**
 * Check position
 * does a check for a slash or a dollar sign and deals with them appropriately
 * originally added by [RavanH](https://github.com/RavanH) in 1.0.4
 * abstracted to a function in 2.0
 * @author Chris Reynolds
 * @author [RavanH](https://github.com/RavanH)
 * @since 2.0
 */
function check_pos($progress) {
	$pos = strpos($progress, '/');
	if($pos===false) {
		$width = $progress . "%";
		$progress = $progress . " %";
	} else {
		$dollar = strpos($progress, '$');
		if ( $dollar === false ) {
			/**
			 * this could be used for other currencies, potentially, though if it was, it should be changed into a case instead of an if statement
			 */
		} else {
			/**
			 * if there's a dollar sign in the progress, it will break the math
			 * let's strip it out so we can add it back later
			 */
			$progress = str_replace('$', '', $progress);
		}
		$xofy = explode('/',$progress);
		if (!$xofy[1])
			$xofy[1] = 100;
		$percentage = $xofy[0] / $xofy[1] * 100;
		$width = $percentage . "%";
		if ( $dollar === false ) {
			$progress = number_format_i18n( $xofy[0] ) . " / " . number_format_i18n( $xofy[1] );
		} else {
			/**
			 * if there's a dollar sign in the progress, display it manually
			 */
			$progress = '$' . number_format_i18n( $xofy[0] ) . ' / $' . number_format_i18n( $xofy[1] );
		}
	}
	return array($progress,$width); // pass both the progress and the width back
}

/**
 * Get Progress Bar
 * gets all the parameters passed to the shortcode and constructs the progress bar
 * @param $location - inside, outside, null (default: null)
 * @param $fullwidth - any value (default: null)
 * @param $text - any custom text (default: null)
 * @param $progress - the progress to display (required)
 * @param $option - any applicable options (default: null)
 * @param $width - the width of the progress bar, based on $progress (required)
 * @param $color - custom color for the progress bar (default: null)
 * @param $gradient - custom gradient value, in decimals (default: null)
 * @param $gradient_end gradient end color, based on the endcolor parameter or $gradient (default: null)
 * @author Chris Reynolds
 * @since 2.0
 */
function get_progress_bar($location = false, $text = false, $progress, $option = false, $width, $fullwidth = false, $color = false, $gradient = false, $gradient_end = false) {
  /**
   * here's the html output of the progress bar
   */
  $output  = "<div class=\"wpcfb-wrapper $location"; // adding $location to the wrapper class, so I can set a width for the wrapper based on whether it's using div.wpcfb-wrapper.after or div.wpcfb-wrapper.inside or just div.wpcfb-wrapper
  if ( $fullwidth ) {
    $output .= " full";
  }
  $output .= "\">";
  if ( $location && $text) { // if $location is not empty and there's custom text, add this
    $output .= "<div class=\"$location\">" . wp_kses($text, array()) . "</div>";
  } elseif ( $location && !$text ) { // if the $location is set but there's no custom text
    $output .= "<div class=\"$location\">";
    $output .= $progress;
    $output .= "</div>";
  } elseif ( !$location && $text) { // if the location is not set, but there is custom text
    $output .= "<div class=\"inside\">" . wp_kses($text, array()) . "</div>";
  }
  $output   .=   "<div class=\"wpcfb-progress";
  if ($fullwidth) {
    $output .= " full";
  } else {
    $output .= " fixed";
  }
  $output   .= "\">";
  $output  .= "<span";
  if ($option) {
    $output .= " class=\"{$option}\"";
  }
  if ($color) { // if color is set
    $output .= " style=\"width: $width; background: {$color};";
    if ($gradient_end) {
      $output .= "background: -moz-linear-gradient(top, {$color} 0%, $gradient_end 100%); background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,{$color}), color-stop(100%,$gradient_end)); background: -webkit-linear-gradient(top, {$color} 0%,$gradient_end 100%); background: -o-linear-gradient(top, {$color} 0%,$gradient_end 100%); background: -ms-linear-gradient(top, {$gradient} 0%,$gradient_end 100%); background: linear-gradient(top, {$color} 0%,$gradient_end 100%); \"";
    }
  } else {
    $output .= " style=\"width: $width;";
  }
  if ($gradient && $color) {
    $output .= "background: -moz-linear-gradient(top, {$color} 0%, $gradient_end 100%); background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,{$color}), color-stop(100%,$gradient_end)); background: -webkit-linear-gradient(top, {$color} 0%,$gradient_end 100%); background: -o-linear-gradient(top, {$color} 0%,$gradient_end 100%); background: -ms-linear-gradient(top, {$gradient} 0%,$gradient_end 100%); background: linear-gradient(top, {$color} 0%,$gradient_end 100%); \"";
  } else {
    $output .= "\"";
  }
  $output  .= "><span></span></span>";
  $output  .=  "</div>";
  $output  .= "</div>";
  /**
   * now return the progress bar
   */
  return $output;
}