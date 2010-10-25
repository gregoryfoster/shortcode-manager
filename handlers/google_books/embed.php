<?php
namespace sc_mgr\handlers\google_books\embed;
/*
Handler Name: Google Books (Embed)
Version: 0.1
Description: Configures and generates output for the Google Books (Embed) shortcode.
Author: Gregory Foster
Author URI: http://entersection.com/
Plugin URI: http://entersection.com/
shortcode-manager-google_books_embed

Usage:
[sc_mgr type="google_books/embed" 
    id="Book ID (ISBN:, OCLC:, LCCN:, Google)" 
    page="page in book" 
    width="width in pixels" 
    height="height in pixels"]
"Text to highlight"
[/sc_mgr]

*/

function default_attributes(){
  return array(
      'type'   => 'google_books/embed',
      'id'     => '',
      'page'   => '',
      'width'  => 425,
      'height' => 650,
  );
}


# Toggle code and styling:
# http://www.sohtanaka.com/web-design/easy-toggle-jquery-tutorial/
function generate_output($attributes){
  extract($attributes);
  
  $output =<<<END_OUTPUT
    <div class="book_viewer">
      <h3 class="trigger"><a href="javascript:void(0);">Google Book Viewer</a></h3>
      <div class="toggle_container">
        <div class="metadata" title="$id" style="width: ${width}px;"></div>
        <div class="canvas" title="$page" style="width: ${width}px; height: ${height}px; min-height: ${height}px;">$body_content</div>
        <div class="canvas_footer">
          <a class="previous_page" href="javascript:void(0);">&laquo;&nbsp;Prev</a> | <a class="jump_to_start_page" href="javascript:void(0);">Jump to start page ($page)</a> | <a class="get_current_page" href="javascript:void(0);">Get current page:</a> <span class="current_page">$page</span> | <a class="next_page" href="javascript:void(0);">Next&nbsp;&raquo;</a>
        </div>
      </div>
    </div>
END_OUTPUT;

  return $output;
}


# Adapted from Apture script handler.
$google_books_script_added_this_request = 0;

function add_google_books_script(){
  global $google_books_script_added_this_request;

  if( !$google_books_script_added_this_request ){
    $relative_script_path = '/shortcode-manager/handlers/google_books/embed.js';
    $script_path = WP_PLUGIN_DIR . $relative_script_path;
    $script_url  = WP_PLUGIN_URL . $relative_script_path;

    if( file_exists($script_path) ){
      echo <<<END_OUTPUT
        <script type='text/javascript' src='http://www.google.com/jsapi'></script>
        <script type='text/javascript'>google.load('books', '0');</script>
        <script type='text/javascript' src='$script_url' charset='utf-8'></script>
END_OUTPUT;
      $google_books_script_added_this_request = 1;
    }
    else {
      echo "<strong>Google Books script not found at '$script_url'</strong>\n";
    }
  }
}
add_action('wp_footer', '\sc_mgr\handlers\google_books\embed\add_google_books_script');


$google_books_stylesheet_added_this_request = 0;

function add_google_books_stylesheet(){
  global $google_books_stylesheet_added_this_request;

  if( !$google_books_stylesheet_added_this_request ){
    $relative_stylesheet_path = '/shortcode-manager/handlers/google_books/embed.css';
    $stylesheet_path = WP_PLUGIN_DIR . $relative_stylesheet_path;
    $stylesheet_url  = WP_PLUGIN_URL . $relative_stylesheet_path;

    if( file_exists($stylesheet_path) ){
      echo "<link rel='stylesheet' type='text/css' href='$stylesheet_url'/>\n";
      $google_books_stylesheet_added_this_request = 1;
    }
    else {
      echo "<!-- Google Books stylesheet not found at '$stylesheet_url' -->\n";
    }
  }
}
# Action added as part of plugin initialization in handler_actions.php

?>
