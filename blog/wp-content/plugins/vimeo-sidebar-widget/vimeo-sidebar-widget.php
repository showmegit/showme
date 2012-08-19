<?php
/*
Plugin Name: Vimeo Sidebar Widget
Plugin URI: http://denzeldesigns.com/
Version: 2.1
Description: A simple sidebar widget to display vimeo video. Updated for Wordpress 2.8. Do not upgrade if you are using WordPress 2.7.1
Author: Denzel Chia
Author URI: http://denzeldesigns.com/
*/ 


add_action( 'widgets_init', 'load_vimeo_sidebar_widget' );

function load_vimeo_sidebar_widget() {
	register_widget( 'VimeoSidebarWidget' );
}

class VimeoSidebarWidget extends WP_Widget {

	function VimeoSidebarWidget() {

	$widget_ops = array( 'classname' => 'vimeosidebar', 'description' => __('A Vimeo Sidebar Widget to display a single video in sidebar', 'vimeosidebar') );


		$control_ops = array( 'width' => 200, 'height' => 350, 'id_base' => 'vimeosidebar' );


		$this->WP_Widget( 'vimeosidebar', __('Vimeo Sidebar Widget', 'vimeosidebar'), $widget_ops, $control_ops );
	}


	function widget( $args, $instance ) {
		extract( $args );


		$title = apply_filters('widget_title', $instance['title'] );
		$v_width = $instance['v_width'];
		$v_height = $instance['v_height'];
		$v_autoplay = $instance['v_autoplay'];
		$v_loop = $instance['v_loop'];
		$v_id = $instance['v_id'];


		echo $before_widget;


		if ( $title )
			echo $before_title . $title . $after_title;

echo '<div id="vimeowidget"><object width="',$v_width,'" height="',$v_height,'"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=',$v_id,'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;loop=',$v_loop,'&amp;color=&amp;fullscreen=1&amp;autoplay=',$v_autoplay,'" /><param name="wmode" value="transparent"><embed src="http://vimeo.com/moogaloop.swf?clip_id=',$v_id,'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;loop=',$v_loop,'&amp;color=&amp;fullscreen=1&amp;autoplay=',$v_autoplay,'" type="application/x-shockwave-flash" wmode="transparent" allowfullscreen="true" allowscriptaccess="always" width="',$v_width,'" height="',$v_height,'"></embed></object></div>';


		echo $after_widget;
	}


	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;


		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['v_width'] = strip_tags( $new_instance['v_width'] );
		$instance['v_height'] = strip_tags( $new_instance['v_height'] );
		$instance['v_loop'] = strip_tags( $new_instance['v_loop'] );
		$instance['v_autoplay'] = strip_tags( $new_instance['v_autoplay'] );
		$instance['v_id'] = strip_tags( $new_instance['v_id'] );

		return $instance;
	}


	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'v_width' => '', 'v_height' => '', 'v_loop' => '','v_autoplay' => '','v_id' => '') );
	    $instance['v_title'] = strip_tags( $instance['v_title'] );
		$instance['v_width'] = strip_tags( $instance['v_width'] );
		$instance['v_height'] = strip_tags( $instance['v_height'] );
		$instance['v_loop'] = strip_tags( $instance['v_loop'] );
		$instance['v_autoplay'] = strip_tags( $instance['v_autoplay'] );
		$instance['v_id'] = strip_tags( $instance['v_id'] );
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" /></label></p>
			<p>
			  <label for="<?php echo $this->get_field_id('v_id'); ?>">Video ID: 
			  <input class="widefat" id="<?php echo $this->get_field_id('v_id'); ?>" name="<?php echo $this->get_field_name('v_id'); ?>" type="text" value="<?php echo $instance['v_id']; ?>" /></label></p>
			<p>
			  <label for="<?php echo $this->get_field_id('v_width'); ?>">Width: 
			  <input class="widefat" id="<?php echo $this->get_field_id('v_width'); ?>" name="<?php echo $this->get_field_name('v_width'); ?>" type="text" value="<?php echo $instance['v_width']; ?>" /></label></p>
              <p>
			  <label for="<?php echo $this->get_field_id('v_height'); ?>">Height: 
			  <input class="widefat" id="<?php echo $this->get_field_id('v_height'); ?>" name="<?php echo $this->get_field_name('v_height'); ?>" type="text" value="<?php echo $instance['v_height']; ?>" /></label></p>
              <p>
			<label for="<?php echo $this->get_field_id( 'v_loop' ); ?>">Continous Loop?:</label> 
<select id="<?php echo $this->get_field_id( 'v_loop' ); ?>" name="<?php echo $this->get_field_name( 'v_loop' ); ?>" class="widefat" style="width:100%;">
            <option value='0'<?php  if($instance['v_loop'] == '0'){echo 'selected="selected"';}?>>No</option>
            <option value='1'<?php  if($instance['v_loop'] == '1'){echo 'selected="selected"';}?>>Yes</option>
			</select>
		</p>
         <p>
			<label for="<?php echo $this->get_field_id( 'v_autoplay' ); ?>">Auto Play?:</label> 
			<select id="<?php echo $this->get_field_id( 'v_autoplay' ); ?>" name="<?php echo $this->get_field_name( 'v_autoplay' ); ?>" class="widefat" style="width:100%;">
            <option value='0'<?php  if($instance['v_autoplay'] == '0'){echo 'selected="selected"';}?>>No</option>
            <option value='1'<?php  if($instance['v_autoplay'] == '1'){echo 'selected="selected"';}?>>Yes</option>
      		</select>
		</p>
                     
  <?php

	}

}


?>