<?php
namespace sc_mgr\handlers\google_books\page_link;
/*
Handler Name: Google Books (Page Link)
Version: 0.1
Description: Configures and generates output for the Google Books (Page Link) shortcode.
Author: Gregory Foster
Author URI: http://entersection.com/
Plugin URI: http://entersection.com/
shortcode-manager-google_books_page_link

Usage:
[sc_mgr type="google_books/page_link" 
    id="Google Books ID" 
    page="page in book"]
"Text to highlight"
[/sc_mgr]

*/

function default_attributes(){
  return array(
      'type'   => 'google_books/page_link',
      'id'     => '',
      'page'  => '',
  );
}


function generate_output($attributes){
  extract($attributes);
  
  # Encode the text to highlight.
  $search_query = urlencode($body_content);
  $highlight = rawurlencode($body_content);
  
  $output =<<<END_OUTPUT
    <a href="http://books.google.com/books?id=$id&q=$search_query#v=onepage&q=$highlight&f=false">p.&nbsp;$page</a>
END_OUTPUT;

  return $output;
}

?>