<?php
/**
 * @package Relative_Site_URL_on_Content_Save
 * @version 1.0.0
 */
/*
Plugin Name: Relative Site URL on Content Save
Plugin URI: http://chrisbratlien.com/2010/05/16/relative-site-url-on-content-save
Description: Replaces occurances of your site's base URL in your content with working [url] shortcode
Author: Chris Bratlien
Version: 1.0.0
Author URI: http://chrisbratlien.com/
*/


function bsdrelative_content_replace_url($content) {  
  return str_replace(get_bloginfo('url'),'[url]',$content);
}
add_filter('content_save_pre','bsdrelative_content_replace_url');

function bsdrelative_url_shortcode($atts, $content = null) {
  return get_bloginfo('url'); 
}
add_shortcode("url", "bsdrelative_url_shortcode"); 
?>
