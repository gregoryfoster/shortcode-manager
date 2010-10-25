<?php
namespace sc_mgr\handlers\youtube\embed;
/*
Handler Name: YouTube
Version: 0.1
Description: Configures and generates output for the YouTube shortcode.
Author: Gregory Foster
Author URI: http://entersection.com/
Plugin URI: http://entersection.com/
shortcode-manager-youtube_embed

Usage:
[sc_mgr type="youtube/embed" 
    id="YouTube movie ID" 
    width="width in pixels" 
    height="height in pixels" 
    start="start time in seconds" 
    end="end time in seconds"]
*/

function default_attributes(){
  return array(
      'type'   => 'youtube/embed',
      'id'     => '',
      'width'  => 445,
      'height' => 364,
      'start'  => 0,
      'end'    => '',
  );
}


function generate_output($attributes){
  extract($attributes);
  $output =<<<END_OUTPUT
  <div class="embed">
    <object width="$width" height="$height">
      <param name="movie" value="http://www.youtube.com/v/$id&start=$start"></param>
      <param name="allowFullScreen" value="true"></param>
      <param name="allowscriptaccess" value="always"></param>
      <embed src="http://www.youtube.com/v/$id&start=$start" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="$width" height="$height"></embed>
    </object>
  </div>
END_OUTPUT;
  return $output;
}

?>