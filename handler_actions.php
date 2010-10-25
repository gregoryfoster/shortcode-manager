<?php
namespace sc_mgr;
/*
This file contains any requirements from Shortcode Manager handlers to hook
up their actions in advance of being called during a request.  This happens
in the circumstance where a handler would like to include content in the HEAD
of an HTML document (as the handler code will otherwise never be loaded prior
to processing the HEAD of any request).

To pre-load the handler code and add any actions, add them to the associative 
array at the start of the file.  These items will be processed by the Shortcode
Manager plugin on initialization.
*/

# Array entries should follow this convention (the same parameters as add_action, 
# just reversed to ensure uniqueness in the associative array):
# '\fully\qualified\namespace\plus\function_name' => 'target_hook_tag',
function get_handler_actions(){
  return array(
    '\sc_mgr\handlers\google_books\embed\add_google_books_stylesheet' => 'wp_print_styles',
  );
}

?>