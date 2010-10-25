<?php
namespace sc_mgr;
/*
Plugin Name: ShortCode Manager
Version: 0.1
Description: Manage shortcodes for the display of content in posts.  Requires PHP 5.3 for namespacing.
Author: Gregory Foster
Author URI: http://entersection.com/
Plugin URI: http://entersection.com/
shortcode-manager
*/

/* WordPress version check. */
global $wp_version;
$target_version = '2.8.6';
if( version_compare($wp_version, $target_version, '<') ){ 
  exit("ShortCode Manager requires WordPress $target_version or newer.  <a href='http://codex.wordpress.org/Upgrading_WordPress'>Please update!</a>");
}

# TODO: test for PHP >= 5.3


function add_handler_actions(){
  # Include the file containing the handlers' requested initialization actions.
  $plugin_path = dirname(__FILE__);
  include_once "${plugin_path}/handler_actions.php";
  $handler_actions = get_handler_actions();
  
  foreach( $handler_actions as $function_name => $target_hook ){
    # Construct the handler path from the function namespace.
    $handler_relative_path = implode('/', array_slice(explode("\\", $function_name), 2, -1));
    $handler_filename = "${plugin_path}/${handler_relative_path}.php";

    # Pre-load the handler code and add the requested action.
    if( file_exists($handler_filename) ){
      include_once $handler_filename;
      add_action($target_hook, $function_name);
    }
    else {
      echo sprintf("<strong>Invalid handler: no handler defined for function '%s' available at '%s'</strong>", $function_name, $handler_filename);
    }
  }
}
add_action('init', '\sc_mgr\add_handler_actions');


function shortcode_handler($raw_attributes, $body_content = null){
  # Short-circuit test for "type" attribute.
  if( !array_key_exists('type', $raw_attributes) ){
    return "<strong>Invalid shortcode: missing 'type' attribute.</strong>";
  }

  # Construct the handler namespace from the given type.
  $type = $raw_attributes['type'];
  $handler_relative_namespace = str_replace('/', "\\", $type);
  $handler_namespace = "\\sc_mgr\\handlers\\$handler_relative_namespace";

  # Construct the default_attributes function to short-circuit file inclusion code.
  $default_attributes_function = "${handler_namespace}\\default_attributes";
  
  if( !function_exists($default_attributes_function) ){
    # Include requested handler.
    $plugin_path = dirname(__FILE__);
    $handler_filename = "${plugin_path}/handlers/${type}.php";
    if( file_exists($handler_filename) ){
      include_once $handler_filename;
    }
    else {
      return sprintf("<strong>Invalid shortcode: no handler defined for type '%s' available at '%s'</strong>", $type, $handler_filename);
    }
  }
  
  # Perform attribute normalization for the type of shortcode requested.
  $default_attributes = $default_attributes_function();
  $normalized_attributes = shortcode_atts($default_attributes, $raw_attributes);
  
  # Add the body content of the shortcode to the collection of normalized attributes.
  # But first: decode HTML entities, strip whitespace, then get rid of windows quotes and dashes.
  $body_content = trim(html_entity_decode($body_content, ENT_QUOTES, 'UTF-8'));
  $body_content = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $body_content);
  $body_content = iconv('ISO-8859-1', 'UTF-8', $body_content);
  $normalized_attributes['body_content'] = $body_content;
  
  # Generate and return output.
  $generate_output_function = "${handler_namespace}\\generate_output";
  return $generate_output_function($normalized_attributes);
}
add_shortcode('sc_mgr', '\sc_mgr\shortcode_handler');

?>
