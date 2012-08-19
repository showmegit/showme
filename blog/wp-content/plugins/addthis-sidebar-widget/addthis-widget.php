<?php
/*
Plugin Name: AddThis Sidebar Widget
Plugin URI: http://foolip.org/blog/2007/05/06/addthis-sidebar-widget/
Description: AddThis subscribe/bookmark buttons for your sidebar.
Author: Philip Jägenstedt
Version: 1.2
Author URI: http://foolip.org/blog/
*/

class AddThisSubscribeWidget {
  var $buttons = array(array('url'=>'http://s5.addthis.com/button0-fd.gif',
			     'width'=>83, 'height'=>16),
		       array('url'=>'http://s5.addthis.com/button0-rss.gif',
			     'width'=>83, 'height'=>16),
		       array('url'=>'http://s5.addthis.com/button1-fd.gif',
			     'width'=>125, 'height'=>16),
		       array('url'=>'http://s5.addthis.com/button1-rss.gif',
			     'width'=>125, 'height'=>16),
		       array('url'=>'http://s5.addthis.com/button2-fd.png',
			     'width'=>160, 'height'=>24),
		       array('url'=>'http://s5.addthis.com/button2-rssfeed.png',
			     'width'=>160, 'height'=>24));

  // static init callback
  function init() {
    // Check for the required plugin functions. This will prevent fatal
    // errors occurring when you deactivate the dynamic-sidebar plugin.
    if ( !function_exists('register_sidebar_widget') )
      return;

    $widget = new AddThisSubscribeWidget();

    // This registers our widget so it appears with the other available
    // widgets and can be dragged and dropped into any active sidebars.
    register_sidebar_widget('AddThis Subscribe', array($widget,'display'));

    // This registers our optional widget control form.
    register_widget_control('AddThis Subscribe', array($widget,'control'), 280, 300);
  }

  // This is the function outputs the AddThis Subscribe button
  function display($args) {

    // $args is an array of strings that help widgets to conform to
    // the active theme: before_widget, before_title, after_widget,
    // and after_title are the array keys. Default tags: li and h2.
    extract($args);

    $options = get_option('widget_at_subscribe');
    $title = $options['title'];
    $username = urlencode($options['username']);
    $feedurl = urlencode($options['feedurl']);
    $url = "http://www.addthis.com/feed.php?pub=$username&amp;h1=$feedurl";
    switch($options['window']) {
    case 'new':
      $window = 'target="_blank"';
      break;
    case 'popup':
      $window = "onclick=\"window.open('$url','addthis-subscribe','scrollbars=yes,menubar=no,width=620,height=520,resizable=yes,toolbar=no,location=no,status=no'); return false;\"";
      break;
    case 'same':
    default:
      $window = '';
    }
    $img = $this->buttons[$options['button']];

    // These lines generate our output.
    echo $before_widget;
    if ($title)
      echo $before_title . $title . $after_title;
    echo '<div style="text-align:center"><a href="'.$url.'" '.$window.' title="' . __('Subscribe using any feed reader!') . '"><img src="'.$img['url'].'"  width="'.$img['width'].'" height="'.$img['height'].'" alt="' . __('AddThis Feed Button') . '" /></a></div>';
    echo $after_widget;
  }

  // This is the function that outputs the control form
  function control() {
    // Get our options and see if we're handling a form submission.
    $options = get_option('widget_at_subscribe');
    if ( !is_array($options) )
      $options = array('title'=>'',
		       'username' => '',
		       'feedurl' => get_bloginfo('rss2_url'),
		       'button' => 2,
		       'window' => 'same');

    if ( $_POST['at-subscribe-submit'] ) {
      // Remember to sanitize and format use input appropriately.
      $options['title'] = strip_tags(stripslashes($_POST['at-subscribe-title']));
      $options['username'] = $_POST['at-subscribe-username'];
      $options['feedurl'] = $_POST['at-subscribe-feedurl'];
      $options['button'] = $_POST['at-subscribe-button'];
      $options['window'] = $_POST['at-subscribe-window'];
      update_option('widget_at_subscribe', $options);
    }

    // Be sure you format your options to be valid HTML attributes.
    $title = htmlspecialchars($options['title'], ENT_QUOTES);
    $username = htmlspecialchars($options['username'], ENT_QUOTES);
    $feedurl = htmlspecialchars($options['feedurl'], ENT_QUOTES);
    $button = $options['button'];
    $window = $options['window'];

    // Here is our little form segment. Notice that we don't need a
    // complete form. This will be embedded into the existing form.
    echo '<p style="text-align:right"><label for="at-subscribe-title">' . __('Title:') . ' <input style="width: 200px" id="at-subscribe-title" name="at-subscribe-title" type="text" value="'.$title.'" /></label></p>';
    echo '<p style="text-align:right"><label for="at-subscribe-username">' . __('Username:') . ' <input style="width: 200px" id="at-subscribe-username" name="at-subscribe-username" type="text" value="'.$username.'" /></label></p>';
    echo '<p style="text-align:right"><label for="at-subscribe-feedurl">' . __('Feed URL:') . ' <input style="width: 200px" id="at-subscribe-feedurl" name="at-subscribe-feedurl" type="text" value="'.$feedurl.'" /></label></p>';

    echo '<p style="text-align:right"><label for="at-subscribe-window">' . __('Open in:') . ' <select style="width: 200px" id="at-subscribe-window" name="at-subscribe-window">';
    echo '<option value="same" '.($window=='same'?'selected="selected"':'').'>Same window</option>';
    echo '<option value="new" '.($window=='new'?'selected="selected"':'').'>New window</option>';
    echo '<option value="popup" '.($window=='popup'?'selected="selected"':'').'>Popup window</option>';
    echo '</select></label></p>';

    echo '<p style="text-align:left;margin-left:80px">';
    foreach ($this->buttons as $i => $img)
      echo '<label for="at-subscribe-button-'.$i.'"><input style="vertical-align:middle;border:none" id="at-subscribe-button-'.$i.'" name="at-subscribe-button" type="radio" value="'.$i.'" '.($button==$i ? 'checked="checked"' : '').' /><img style="margin:2px;vertical-align:middle" src="'.$img['url'].'" width="'.$img['width'].'" height="'.$img['height'].'" alt="" /></label><br />';
    echo '</p>';

    echo '<input type="hidden" id="at-subscribe-submit" name="at-subscribe-submit" value="1" />';
  }
}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', array('AddThisSubscribeWidget','init'));



class AddThisBookmarkWidget {
  var $buttons = array(array('url'=>'http://s5.addthis.com/button0-bm.gif',
			     'width'=>83, 'height'=>16),
		       array('url'=>'http://s5.addthis.com/button1-bm.gif',
			     'width'=>125, 'height'=>16),
		       array('url'=>'http://s5.addthis.com/button2-bm.png',
			     'width'=>160, 'height'=>24));

  // static init callback
  function init() {
    // Check for the required plugin functions. This will prevent fatal
    // errors occurring when you deactivate the dynamic-sidebar plugin.
    if ( !function_exists('register_sidebar_widget') )
      return;

    $widget = new AddThisBookmarkWidget();

    // This registers our widget so it appears with the other available
    // widgets and can be dragged and dropped into any active sidebars.
    register_sidebar_widget('AddThis Bookmark', array($widget,'display'));

     // This registers our optional widget control form.
    register_widget_control('AddThis Bookmark', array($widget,'control'), 280, 200);
  }

  // This is the function outputs the AddThis Social Bookmark button
  function display($args) {
    // $args is an array of strings that help widgets to conform to
    // the active theme: before_widget, before_title, after_widget,
    // and after_title are the array keys. Default tags: li and h2.
    extract($args);

    $options = get_option('widget_at_bookmark');

    if (is_single()) {
      $linkurl = get_permalink();
      $linktitle = single_post_title('',FALSE);
    } else {
      $parts = parse_url(get_option('siteurl'));
      $linkurl = $parts['scheme'].'://'.$parts['host'].$_SERVER['REQUEST_URI'];
      $linktitle = get_bloginfo('name') . wp_title('&raquo;',FALSE);
    }
    $linkurl = urlencode($linkurl);
    $linktitle = urlencode($linktitle);

    $title = $options['title'];
    $username = urlencode($options['username']);
    $url = "http://www.addthis.com/bookmark.php?pub=$username&amp;url=$linkurl&amp;title=$linktitle";
    switch($options['window']) {
    case 'new':
      $window = 'target="_blank"';
      break;
    case 'popup':
      $window = "onclick=\"window.open('$url','addthis-bookmark','scrollbars=yes,menubar=no,width=620,height=520,resizable=yes,toolbar=no,location=no,status=no'); return false;\"";
      break;
    case 'same':
    default:
      $window = '';
    }
    $img = $this->buttons[$options['button']];

    // These lines generate our output.
    echo $before_widget;
    if ($title)
      echo $before_title . $title . $after_title;
    echo '<div style="text-align:left; border: 1px #ddd solid; padding: 10px;"><a href="'.$url.'" '.$window.' title="' . __('Bookmark using any bookmark manager!') . '"><img src="'.$img['url'].'"  width="'.$img['width'].'" height="'.$img['height'].'" alt="' . __('AddThis Social Bookmark Button') . '" /></a></div>';
    echo $after_widget;
  }

  // This is the function that outputs the control form
  function control() {
    // Get our options and see if we're handling a form submission.
    $options = get_option('widget_at_bookmark');
    if ( !is_array($options) )
      $options = array('title'=>'',
		       'username' => '',
		       'button' => 1,
		       'window' => 'same');

    if ( $_POST['at-bookmark-submit'] ) {
      // Remember to sanitize and format use input appropriately.
      $options['title'] = strip_tags(stripslashes($_POST['at-bookmark-title']));
      $options['username'] = $_POST['at-bookmark-username'];
      $options['button'] = $_POST['at-bookmark-button'];
      $options['window'] = $_POST['at-bookmark-window'];
      update_option('widget_at_bookmark', $options);
    }

    // Be sure you format your options to be valid HTML attributes.
    $title = htmlspecialchars($options['title'], ENT_QUOTES);
    $username = htmlspecialchars($options['username'], ENT_QUOTES);
    $button = $options['button'];
    $window = $options['window'];

    // Here is our little form segment. Notice that we don't need a
    // complete form. This will be embedded into the existing form.
    echo '<p style="text-align:right"><label for="at-bookmark-title">' . __('Title:') . ' <input style="width: 200px" id="at-bookmark-title" name="at-bookmark-title" type="text" value="'.$title.'" /></label></p>';
    echo '<p style="text-align:right"><label for="at-bookmark-username">' . __('Username:') . ' <input style="width: 200px" id="at-bookmark-username" name="at-bookmark-username" type="text" value="'.$username.'" /></label></p>';

    echo '<p style="text-align:right"><label for="at-bookmark-window">' . __('Open in:') . ' <select style="width: 200px" id="at-bookmark-window" name="at-bookmark-window">';
    echo '<option value="same" '.($window=='same'?'selected="selected"':'').'>Same window</option>';
    echo '<option value="new" '.($window=='new'?'selected="selected"':'').'>New window</option>';
    echo '<option value="popup" '.($window=='popup'?'selected="selected"':'').'>Popup window</option>';
    echo '</select></label></p>';

    echo '<p style="text-align:left;margin-left:80px">';
    foreach ($this->buttons as $i => $img)
      echo '<label for="at-bookmark-button-'.$i.'"><input style="vertical-align:middle;border:none" id="at-bookmark-button-'.$i.'" name="at-bookmark-button" type="radio" value="'.$i.'" '.($button==$i ? 'checked="checked"' : '').' /><img style="margin:2px;vertical-align:middle" src="'.$img['url'].'" width="'.$img['width'].'" height="'.$img['height'].'" alt="" /></label><br />';
    echo '</p>';

    echo '<input type="hidden" id="at-bookmark-submit" name="at-bookmark-submit" value="1" />';
  }
}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', array('AddThisBookmarkWidget','init'));
?>
