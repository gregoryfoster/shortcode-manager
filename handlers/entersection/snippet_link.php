<?php
namespace sc_mgr\handlers\entersection\snippet_link;
/*
Handler Name: Entersection (Snippet Link)
Version: 0.1
Description: Configures and generates output for the Entersection (Snippet Link) shortcode.
Author: Gregory Foster
Author URI: http://entersection.com/
Plugin URI: http://entersection.com/
shortcode-manager-entersection_snippet_link

Usage:
[sc_mgr type="entersection/snippet_link" 
    filename="filename of snippet image"
    page="page in book"]
"Fancybox caption text."
[/sc_mgr]
*/

function default_attributes(){
  return array(
      'type'     => 'entersection/snippet_link',
      'filename' => '',
      'page'     => '',
  );
}


function generate_output($attributes){
  extract($attributes);
  
  # Search for the requested image in the upload subdirectories.
  $wp_uploads = wp_upload_dir();
  $target_paths = find_file($wp_uploads['basedir'], $filename);
  
  if( count($target_paths) == 0 ){
    return "<strong>Unable to locate requested filename: '$filename'</strong>";
  }

  # Construct file URI.
  ereg("^.*/wp-content/uploads(.*)$", $target_paths[0], $matches);
  $target_uri = $wp_uploads['baseurl'] . $matches[1];
  
  # Construct icon URI.
  $template_directory = get_bloginfo('template_directory');
  $icon_uri = $template_directory . '/i/icon-page.jpg';
  
  # Ensure image has a title.
  $title = $body_content;
  
  $output =<<<END_OUTPUT
    <a href="$target_uri"><img src="$icon_uri" title="$title" style="margin-right: 2px;"/>p.&nbsp;$page</a>
END_OUTPUT;

  return $output;
}

// Adapted from:
// http://lixlpixel.org/recursive_function/php/recursive_directory_scan/
function find_file($directory, $target_filename){
  $target_matches = array();
  
  // If the path has a slash at the end we remove it here
  if( substr($directory, -1) == '/' ){
    $directory = substr($directory,0,-1);
  }

  // Check if the path is not valid or is not a directory.
  if( !file_exists($directory) || !is_dir($directory) ){
    return $target_matches;
  }
  elseif( is_readable($directory) ){
    $directory_handle = opendir($directory);

    // Scan through the directory contents.
    while( FALSE !== ($filename = readdir($directory_handle)) ){
      if( $filename != '.' && $filename != '..' ){
        $path = $directory . '/' . $filename;

        if( is_readable($path) ){
          if( is_dir($path) ){
            $subdirectory_matches = find_file($path, $target_filename);
            $target_matches = array_merge($target_matches, $subdirectory_matches);
          }
          elseif( is_file($path) && basename($path) == $target_filename ){
            $target_matches[] = $path;
          }
        }
      }
    }

    closedir($directory_handle);
    return $target_matches;
  } 
  else {
    return $target_matches;
  }
}

?>
