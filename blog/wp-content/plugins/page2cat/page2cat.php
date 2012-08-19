<?php
/*
Plugin Name: Category Page 
Plugin URI: http://pixline.net/wordpress-plugins/category-page-wordpress-plugin/en/
Description: Use pages as category archive header, or list some posts from a category into a post/page. 
Author: Pixline
Version: 2.5
Author URI: http://pixline.net/

Copyright (C) 2008 Paolo Tresso / Pixline (http://pixline.net/)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.


*/
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }


define("PAGE2CAT_VERSION","2.5");
define("PLUGIN_PATH",get_bloginfo('url')."/wp-content/plugins/page2cat/");

define('PIXLINE_FOOTER','<p><hr style="margin-top:50px;height:1px;background:#CCC;border:0px;clear:both;" />
<strong>
<a href="http://pixline.net/wordpress-plugins/category-page-wordpress-plugin/en/">Category Page</a></strong> plugin 
<small> | 
<a href="http://talks.pixline.net/forum.php?id=3">Support Forum</a> | 
GPL2&copy; &ge; 2007 <a href="http://pixline.net/about/en/">Paolo Tresso / Pixline</a> | 
If you like this plugin, support its development with a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paolo%40pixline%2enet&item_name=Support%20to%20opensource%20projects%20at%20Pixline&no_shipping=1&cn=Something%20to%20say%3f&tax=0&currency_code=EUR&lc=IT&bn=PP%2dDonationsBF&charset=UTF%2d8">small donation</a>.</p>');

$wpdb->page2cat	= $table_prefix . 'page2cat';

######################################################
# check version. #
######################################################

$p2c_version = get_option('pixline_page2cat_version');
	register_activation_hook(__FILE__, 'page2cat_install');

// DB installation. usually happen just one time.
function page2cat_install(){
global $wpdb, $p2c_version;

if ($version < 6):
	$query = "
	CREATE TABLE  `".$wpdb->prefix."page2cat` (
	 `rel_ID` BIGINT NOT NULL AUTO_INCREMENT ,
	 `page_ID` BIGINT NOT NULL ,
	 `cat_ID` BIGINT NOT NULL ,
	PRIMARY KEY (  `rel_ID` ) ,
	INDEX (  `page_ID` ,  `cat_ID` )
	);
	";

	if(is_file(ABSPATH . 'wp-admin/includes/upgrade.php')){ 
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	}elseif(is_file(ABSPATH . 'wp-admin/upgrade-functions.php')){
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	}
	
	if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."page2cat'") != $wpdb->prefix."page2cat") {
	dbDelta($query);
	delete_option('pixline_page2cat_version');
	add_option('pixline_page2cat_version', 6);
	}

update_option('p2c_use_empty','false');
update_option('p2c_show_used_pages','false');
update_option('p2c_catlist_limit',5);
update_option('p2c_catlist_title','');
endif;

delete_option('p2c_use_img');		// deprecated, so we're going to delete it anyway.
if( !get_option('p2c_use_empty')) 		update_option('p2c_use_empty','false');
if(!get_option('p2c_show_used_pages')) 	update_option('p2c_show_used_pages','false');
if(!get_option('p2c_catlist_limit')) 	update_option('p2c_catlist_limit',5);
if(!get_option('p2c_catlist_title')) 	update_option('p2c_catlist_title','');
}

######################################
# setup management and option panels #
######################################

// adds a panel under "manage » category pages"
// shows actual connections, and allow creations of new ones.
function page2cat_manage_page(){
global $wpdb, $wp_db_version;

	#print_r($_POST); #die();
	
	// multiple delete
	if( isset($_POST['deleteit']) && !empty($_POST['p2c_reldel'])):
		foreach($_POST['p2c_reldel'] as $key=>$id):
			$query = "DELETE FROM {$wpdb->page2cat} WHERE rel_ID = '".$id."' LIMIT 1;";
			$daje = $wpdb->get_results($query);
		endforeach;
	echo '<div id="message" class="updated fade">
			<p><strong>Some connections deleted.</strong></p></div>';
	endif;

	// multiple link
	if( isset($_POST['action']) && $_POST['p2c_linkto'] == true && $_POST['multipageid'] != ""):
#	print_r($_POST);
		$pageid = $_POST['multipageid'][0];
		foreach($_POST['p2c_reladd'] as $key=>$id):		
		if(trim($id)!=""):
			$query = "INSERT INTO {$wpdb->page2cat} (rel_ID, page_ID, cat_ID) VALUES ('', '".$pageid."', '".$id."');";
			$daje = $wpdb->get_results($query);
		endif;
		endforeach;
		echo '<div id="message" class="updated fade">
			<p><strong>Connections created.</strong></p></div>';
	
	elseif( isset($_POST['action']) && $_POST['p2c_update'] == true):
#	print_r($_POST); die();
		$pageid = $_POST['pageid'];
		foreach($pageid as $cat_id=>$page_id):
			if(trim($page_id) != ""):
			$sql = "INSERT INTO {$wpdb->page2cat} (rel_ID, page_ID, cat_ID) VALUES ('', '".$page_id."', '".$cat_id."');";
			$result = $wpdb->get_results($sql);
			endif;
		endforeach;
		echo('<div id="message" class="updated fade">
		<p><strong>New Category Page created succesfully.</strong></p></div>');
	endif;

	// single delete, confirm request
	if( isset($_REQUEST['p2cdel']) && !isset($_REQUEST['p2c_confirm']) ):
		$relid = $wpdb->escape($_REQUEST['p2cdel']);
		echo "<div class='wrap'><h2>Delete this Category &rsaquo; Page relation?</h2>";
		$opt = $wpdb->get_results("SELECT * FROM {$wpdb->page2cat} WHERE rel_ID = '".$relid."' LIMIT 1;");
		$catid = $opt[0]->cat_ID;
		$pageid = $opt[0]->page_ID;
		$catname = get_cat_name($catid);
		$page = get_page($pageid); 
		$title = $page->post_title;
		echo "<form name='p2c_confirm' method='post'>";
		echo "<p>You are going to delete the relationship between 
		Category <em>".$catname."</em> and Page <em>".$title."</em>. Confirm?</p>";
		echo '<p>
		<input type="hidden" name="p2cdel" value="'.$relid.'" />
		<input type="hidden" name="p2c_confirm" value="true" />
		<input type="reset" name="revert" value="Cancel" />
		<input type="submit" name="confirm" value="DELETE &raquo;" />
		</p></form>';
		echo "</div>";
	endif;

	// confirmed, so delete this connection
	if( isset($_REQUEST['p2cdel']) && $_REQUEST['p2c_confirm'] == 'true'):
	$relid = $wpdb->escape($_REQUEST['p2cdel']);
	$myopts = $wpdb->get_results("DELETE FROM {$wpdb->page2cat} WHERE rel_ID = '".$relid."' LIMIT 1;");
	echo('<div id="message" class="updated fade"><p><strong>You have succesfully deleted a connection.</strong></p></div>');
	echo "<div class='wrap'><a href='edit.php?page=category-pages'>Back to Category Pages manage page</a></div>";
	endif;


	/*
		real pages
	*/
	$count_used = $wpdb->get_var("SELECT count(*) FROM {$wpdb->page2cat};");
	
	if($wp_db_version >= 6124):	// this is WordPress >= 2.3
	$count_full = $wpdb->get_var("SELECT count(*) 
		FROM {$wpdb->terms} AS t LEFT JOIN {$wpdb->term_taxonomy} AS tt 
		ON t.term_id = tt.term_id WHERE tt.taxonomy='category';");

	$count_noempty = $wpdb->get_var("SELECT count(*) 
		FROM {$wpdb->terms} AS t LEFT JOIN {$wpdb->term_taxonomy} AS tt 
		ON t.term_id = tt.term_id WHERE tt.taxonomy='category' AND tt.count != '0';");
	else: // WP < 2.3
	$count_full = $wpdb->get_var("SELECT count(*) FROM {$wpdb->categories};");
	$count_noempty = $wpdb->get_var("SELECT count(*) FROM {$wpdb->categories}; WHERE category_count != '0';");
	endif;
	
	$count_free = $count_full - $count_used;
	if(get_option('p2c_use_empty') == "false"): $count_usable = $count_noempty - $count_used; else : $count_usable = $count_free; endif;
	

	// this is the already connected page


	$myopts = $wpdb->get_results("SELECT * FROM {$wpdb->page2cat} ORDER BY page_ID ASC;");
	$usedcats = array();
	$usedpages = array();

		if($_REQUEST['view'] == 'new'): 
			$css1 = ''; 	$css2 = ' class="current"'; 
			$pagetitle = "<small>New connection</small>"; 
		else: 
			$css1 = ' class="current"'; $css2 = ''; 
			$pagetitle = "<small>Manage connections</small>"; 
		endif;


	if( !isset($_REQUEST['p2cdel'])):
		echo "<div class='wrap'><h2>Category Pages</h2>";
		echo '<ul class="subsubsub">
		<li><a href="edit.php?page=category-pages"'.$css1.'>Manage connections ('.$count_used.')</a> |</li>
		<li><a href="edit.php?page=category-pages&view=new"'.$css2.'>Free categories ('.$count_usable.'/'.$count_free.')</a></li>
		</ul>';

if($_REQUEST['view'] != 'new'):

		if(count($myopts) != 0):
#		echo "<div><p>These categories and these pages are already connected each other. You can view the category page in your website, clicking on the Category name. You can also edit or delete each connection.</p>";

		echo "<div class='tablenav'>";
		echo "<div class='tablenav-pages'>&nbsp;</div>";
		echo '<div style="float: left">
			<form id="p2c_del" name="p2c_del" method="post"/>
			<input type="hidden" name="page" value="category-pages" />
			<input type="hidden" name="view" value="'.$_REQUEST['view'].'" />
			<input type="hidden" name="action" value="p2c_del" />
			<input type="submit" value="Delete Checked" name="deleteit" class="button-secondary delete" />
			</div>';
		echo "<br style='clear:both;' />";
		echo "</div>";
		
		echo "<br style='clear:both;' />";
		echo '
			';
		echo "<table class='widefat'>";
			echo "<thead>";
			echo "<tr>
				<th>";
			if($wp_db_version >= 7098): echo "<input type='checkbox' onclick='checkAll(document.getElementById(\"p2c_del\"));' />" ;
				else: echo "&nbsp;"; endif; 
			echo "</th>
			<th>Host <strong>Category</strong></th>
			<th>Header <strong>Page</strong></th>
			<th>&nbsp;</th>
			</tr>";
			echo "</thead><tbody>";
		foreach($myopts as $opt):
			$catid = $opt->cat_ID;
			$pageid = $opt->page_ID;
			$catname = get_cat_name($catid);
			$usedcats[] = $catid;
			$usedpages[] = $pageid;
			$page = get_page($pageid); 
			$title = $page->post_title;

		echo "<tr>
			<td><input type='checkbox' value='".$opt->rel_ID."' name='p2c_reldel[]' class='p2cbox' /></td>
			<td><strong><a href='../?cat=".$catid."' title='View this Category Page in your website.'>".$catname."</a></strong>
				<!-- (cat #".$catid.") --></td>
			<td><strong><a href='page.php?action=edit&post=".$pageid."' title='Edit this page.'>".$title." 
				<!-- (page #".$pageid.") --></td>
			<td><a href='?page=category-pages&amp;p2cdel=".$opt->rel_ID."' class='delete' title='Delete this connection permanently.'>Delete</a></td>
			</tr>";
		endforeach;

		echo "</tbody>";
		echo "</table>";

		echo "<div class='tablenav'>";
		echo "<div class='tablenav-pages'>&nbsp;</div>";
		echo '<div style="float: left">
			<form name="p2c_del" method="post"/>
			<input type="hidden" name="page" value="category-pages" />
			<input type="hidden" name="view" value="'.$_REQUEST['view'].'" />
			<input type="hidden" name="action" value="p2c_del" />
			<input type="submit" value="Delete Checked" name="deleteit" class="button-secondary delete" />
			</form>
			</div>';
		echo "<br style='clear:both;' />";
		echo "</div>";
		
		else:

		echo "<p>Sorry, no Category &raquo; Page connection available (yet). Maybe you want to <a href='edit.php?page=category-pages&view=new'>create some new connections</a>?</p>";
		endif;


else:
// this is the 'new' page
		
		$usedcats = $wpdb->get_col("SELECT cat_ID FROM {$wpdb->page2cat};");
#		print_r($usedcats);
		$opt3 = get_option('p2c_use_empty');
		if($opt3 == "true"): $empty = false; $excats = ""; elseif($opt3 == "false"): $empty = true; $excats = implode(',',$usedcats); endif;

		$catdef = array('type' => 'post', 'child_of' => 0, 'orderby' => 'name', 'order' => 'ASC',
		'hide_empty' => $empty, 'include_last_update_time' => false, 'hierarchical' => true, 
		'exclude' => $excats, 'include' => '', 'number' => '', 'pad_counts' => true);
		$others = get_categories($catdef);

		$used = count($usedcats);
		$count = count($others);

		if( $count == 0):
		echo "<p>Sorry, you don't have any usable category (or there are some, but empty; <a href='options-general.php?page=category-page-options'>check your settings</a>).</p>";
		else:
#		echo "<h3>New connections</h3>";
#		echo "<div><p>These categories aren't connected with any page. Here you can setup a connection with an existent page, or create a brand new one.</p>";

		echo "<form id='page2cat_manage' name='page2cat_manage' method='post'>";
		$pages_already_used = implode(',',$usedpages);
		if(get_option('p2c_show_used_pages')=='true') $exclude = $pages_already_used; else $exclude ="";

		// wp_dropdown_pages settings (for both boxes in this page)
		$pre_ddprefs = array('depth' => 0, 'child_of' => 0, 'selected' => 0, 
			'echo' => 0,'name' => 'multipageid[]', 'exclude' => $exclude, 
			'show_option_none' => '(none)');


/*		
		echo "<div class='tablenav'>";
		echo "<div class='tablenav-pages'>
			<input type='submit' name='p2c_update' value='Update Category Pages' class='button'/>
			<br style='clear:both;' />
			</div>";
		echo '<div style="float:left;">
			<input type="hidden" name="page" value="category-pages" />
			<input type="hidden" name="view" value="'.$_REQUEST['view'].'" />
			<input type="hidden" name="action" value="p2c_multilink" />
			Link checked to: 
			'.wp_dropdown_pages($pre_ddprefs).'
			<input type="submit" value="Link them!" name="p2c_linkto" class="button-secondary" />
			</div>';
		echo "<br style='clear:both;'/>&nbsp;<br style='clear:both;' />";
		echo "</div>";
*/
			echo "<table class='widefat'>";
			echo "<thead>";
			echo "<tr>
			<th>";
			if($wp_db_version >= 7098): echo "<input type='checkbox' onclick='checkAll(document.getElementById(\"page2cat_manage\"));' />"; 
				else: echo "&nbsp;"; endif; 
			echo "</th>";
			echo "<th>Host Category</th>
			<th>Choose Page</th>
			<th>Create New Page</th>
			</tr>";
			echo "</thead><tbody>";

		
			foreach($others as $otro):
			switch($wp_db_version):
			// "7098"=>"2.5",	"6124"=>"2.3.3", "5183"=>"2.2.1", "4773"=>"2.1.3"
				case ( $wp_db_version < 5183 ):	// WP 2.1.3 and smaller
				$versioned_catid = $otro->cat_ID;
				$versioned_catname = $otro->cat_name;
				$versioned_slug = $otro->category_nicename;
				$versioned_count = $otro->category_count + $otro->posts_private;
				break;
				case ( $wp_db_version >= 5183 ):	// WP 2.2.1 and greater
				default:
				$versioned_catid = $otro->term_id;
				$versioned_catname = $otro->name;
				$versioned_slug = $otro->category_nicename;
				$versioned_count = $otro->count;
				break;
			endswitch;

			// wp_dropdown_pages settings
			$ddprefs = array('depth' => 0, 'child_of' => 0, 'selected' => 0, 
				'echo' => 0,'name' => 'pageid['.$versioned_catid.']', 
				'exclude' => $exclude, 'show_option_none' => '(none)');

			$output = "<tr>
			<td><input type='checkbox' value='".$versioned_catid."' name='p2c_reladd[]' /></td>
			<td>".$versioned_catname." <!-- (".$versioned_count.") --></td>
			<td>".wp_dropdown_pages($ddprefs)."</td>";
			$output .= "<td><a href='page-new.php?p2c=".$versioned_catid."'>Create New</a></td>
			</tr>";
			
			$stringhe[$versioned_catname] = $output;
			endforeach;
			
			asort($stringhe);
			reset($stringhe);
			
			foreach($stringhe as $stringa):
				echo $stringa;
			endforeach;

			echo "</tbody>";
			echo "</table>";

			echo "<div class='tablenav'>";
			echo "<div style='float:right;'>
				<input type='submit' name='p2c_update' value='Update Category Pages' class='button'/>
				</div>";
			echo '<div style="float:left;">
				<input type="hidden" name="page" value="category-pages" />
				<input type="hidden" name="view" value="'.$_REQUEST['view'].'" />
				<input type="hidden" name="action" value="p2c_multilink" />
				Link checked to: 
				'.wp_dropdown_pages($pre_ddprefs).'
				<input type="submit" value="Link them!" name="p2c_linkto" class="button-secondary" />
				</div>';
			echo "<br style='clear:both;'/>";
			echo "</div>";
endif;

		echo "</form>";
		endif;
		echo PIXLINE_FOOTER;
		echo "</div>";
	endif;
}

// adds styles in admin head (WP 2.5+)
function page2cat_metabox_styles(){
?>
<style type="text/css" media="screen">
#p2c-scroller{
	width:100%;
}

ul#p2c-bind li, ul#p2c-free li{
	list-style-type:none;
	width:32%;
	float:left;
	font-size:80%;
}

.p2c-cleaner{
	clear:both;
	height:1px;
}
.p2c-used{
	color:#D66;
	text-decoration:line-through;
}
.p2c-catlink{
	color:#000;
	text-decoration:none;
	border-bottom:1px dotted #CCC;
}
</style>
<?php
wp_enqueue_script('admin-forms');
}

// adds styles in admin head (WP < 2.5)
function page2cat_sidebox_styles(){
?>
<style type="text/css" media="screen">
#p2c-scroller{
	height:120px;
	overflow:auto;
	width:90%;
	margin:0% 5%;
}

#p2c-box p{
	margin:0px;
	padding:5px;
	font-size:80%;
}

ul#p2c-bind, ul#p2c-free,
ul#p2c-bind li, ul#p2c-free li{
	margin:0px;
	padding:0px;
	list-style-type:none;
}

ul#p2c-bind{
	font-size:90%;
	margin:1% 0%;
}


ul#p2c-free{
	font-size:80%;
	color:#333;
	margin:2%;
}

ul#p2c-free li{
	padding:2px 1px;
}

.p2c-count{
	color:#AAA;
}

.p2c-indent{
	padding-left:10px;
}

.p2c-used{
	color:#D66;
	text-decoration:line-through;
}

.p2c-catlink{
	color:#000;
	text-decoration:none;
	border-bottom:1px dotted #CCC;
}

</style>
<?php 
}

// adds sidebox in page editing (until WP 2.3x)
function page2cat_add_sidebox($post_ID){
?>
<fieldset id="p2c-select" class="dbx-box">
<h3 class="dbx-handle">Category Pages</h3>
<div class="dbx-content" id="p2c-box">
	<?php page2cat_add_meta_box($post_ID);	?>
</div>
</fieldset>
<?php


}

function page2cat_add_meta_box($post_ID){
	global $wpdb, $post_ID, $_REQUEST;
#	if(isset($_REQUEST['p2c'])):
#	endif;
	
	$mypage = $wpdb->get_results("SELECT * FROM {$wpdb->page2cat} WHERE page_ID = '".$post_ID."';",OBJECT);
	?>	
		<p>Use this page as <a href='edit.php?page=category-pages'>Category Page</a> for these categories.</p>
	<?php
	$usedcats = array();
	if(count($mypage)>0):
		echo "<ul id='p2c-bind'>";
		foreach($mypage as $connection):
	#		print_r($connection);
			echo "<li><input type='checkbox' checked='checked' value='".$connection->cat_ID."' name='p2c_bind[]' 
			id='p2c".$connection->cat_ID."' /> ".get_cat_name($connection->cat_ID)."</li>
			";
			$usedcats[] = $connection->cat_ID;
		endforeach;
		echo "</ul>";
	endif;

		$opt3 = get_option('p2c_use_empty');
		if($opt3 == "true"): $empty = false; $exclude = ""; 
		elseif($opt3 == "false"): $empty = true; $exclude = implode(',',$usedcats);
		endif;

	$catdef = array('type' => 'post', 'child_of' => 0, 'orderby' => 'name', 'order' => 'ASC', 'exclude' => $exclude, 
					'hide_empty' => $empty, 'include_last_update_time' => false, 'hierarchical' => true, 'pad_counts' => true);

	$others = get_categories($catdef);
	echo "<div id='p2c-scroller'>";
	echo "<ul id='p2c-free'>";
	foreach($others as $cat):
	#print_r($cat); exit();
	if($cat->cat_ID == $_REQUEST['p2c']): $flag = "checked='checked'"; else: $flag = ""; endif;

	$has_page = $wpdb->get_var("SELECT count(*) FROM {$wpdb->page2cat} WHERE cat_ID = '".$cat->cat_ID."'");
	if($has_page == 0):
	echo "<li><input type='checkbox' name='p2c_bind[]' id='p2c".$cat->cat_ID."' value='".$cat->cat_ID."' ".$flag."/> "
	."<a class='p2c-catlink' href='../?cat=".$cat->cat_ID."' title='".get_cat_name($cat->cat_ID)." (".$cat->category_count." items inside).'>".get_cat_name($cat->cat_ID)."</a></li>
	";
	else:
#	echo "<li><input type='checkbox' disabled='disabled' name='p2c_bind[]' id='p2c".$cat->cat_ID."' value='".$cat->cat_ID."'/> <span class='p2c-used'>"
#	.get_cat_name($cat->cat_ID)." </span><span class='p2c-count'>(".$cat->category_count." items)</span></li>
#	";
	$nothing = 0;
	endif;
	endforeach;
	echo "</ul>";
	echo "<br style='clear:both;' />";
	echo "</div>";
	?>
	<?php
}

// trigger page save/edit and make db relations
function page2cat_trigger_save($post_ID){
global $wpdb;

$values = $_POST['p2c_bind'];
$via = "DELETE FROM {$wpdb->page2cat} WHERE page_ID = '".$_POST['post_ID']."'";
$result = $wpdb->get_results($via);
if(count($values) > 0):
foreach($values as $key=>$cat){
	if($_POST['post_ID'] != ""):	
	$sql = "INSERT INTO {$wpdb->page2cat} (rel_ID, page_ID, cat_ID) VALUES ('','".$_POST['post_ID']."','".$cat."')";
	$result = $wpdb->get_results($sql);
	endif;
}
endif;
}

// trigger page deletion and free db relations
function page2cat_trigger_delete(){
	// yes, there's no trigger, actually....
}

// filter the content of a page, check for tag and replace it with a list of posts in the requested category.
// function heavily inspired from Alex Rabe NextGen Gallery's nggfunctions.php. 
function page2cat_content_catlist($content){
global $post;
	if ( stristr( $content, '[catlist' )) {
		$search = "@(?:<p>)*\s*\[catlist\s*=\s*(\w+|^\+)\]\s*(?:</p>)*@i";
		if	(preg_match_all($search, $content, $matches)) {
			if (is_array($matches)) {
				$title = get_option('p2c_catlist_title');
				if($title != "") $output = "<h4>".$title."</h4>"; else $output = "";
				$output .= "<ul class='p2c_catlist'>";
				$limit = get_option('p2c_catlist_limit');
				foreach ($matches[1] as $key =>$v0) {
					$catposts = get_posts('category='.$v0."&numberposts=".$limit);
						foreach($catposts as $single):
						$output .= "<li><a href='".get_permalink($single->ID)."'>".$single->post_title."</a></li>";
						endforeach;
					$search = $matches[0][$key];
					$replace= $output;
					$content= str_replace ($search, $replace, $content);					
				}
			$output .= "</ul>";
			}
		}
	}
return $content;
}

// add real option page
function page2cat_options_page(){
global $wpdb, $styles;

$p2c_defaults = array("show_usedpages_yes"=>"","show_usedpages_no"=>"","use_empty_no"=>"", "use_empty_yes"=>"", "catlist_limit"=>"", "catlist_title"=>"");

if(isset($_POST['page2cat_action'])):
#print_r($_POST); #die();
	$sane1 = strip_tags(htmlentities($_POST['p2c_catlist_limit']));
	update_option('p2c_catlist_limit',$sane1);
	$sane4 = strip_tags(htmlentities($_POST['p2c_catlist_title']));
	update_option('p2c_catlist_title',$sane4);
	$sane2 = strip_tags(htmlentities($_POST['p2c_show_used_pages']));
	update_option('p2c_show_used_pages',$sane2);
	$sane3 = strip_tags(htmlentities($_POST['p2c_use_empty']));
	update_option('p2c_use_empty',$sane3);
	echo('<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>');
endif;

$opt3 = get_option('p2c_use_empty');
if($opt3 == "false"){
	$p2c_defaults['use_empty_no'] = ' selected="selected"';
}elseif($opt3 == "true"){
	$p2c_defaults['use_empty_yes'] = ' selected="selected"';
}

$opt2 = get_option('p2c_show_used_pages');
if($opt2 == "false"){
	$p2c_defaults['show_usedpages_no'] = ' selected="selected"';
}elseif($opt2 == "true"){
	$p2c_defaults['show_usedpages_yes'] = ' selected="selected"';
}

$p2c_catlist_title = get_option('p2c_catlist_title');


	echo "<div class='wrap'>";
	echo "<h2>Category Page settings</h2>";
	echo "<form method='post' name='page2cat_options' accept-charset='utf-8'>";

	echo '
	<h3>Management settings</h3>
	<p>These settings let you customize your <a href="edit.php?page=category-pages">Category Page management panel</a>.</p>
	<table class="form-table">
	<tr valign="top">
	<th scope="row">Show empty categories</th>
	<td>
	<select name="p2c_use_empty">
	<option value="false"'.$p2c_defaults['use_empty_no'].' label=" No, thanks. "> No, thanks. </option>
	<option value="true"'.$p2c_defaults['use_empty_yes'].' label=" Yes, please! "> Yes, please! </option>
	</select> <small>(Choose whether empty categories are shown or not. )</small>
	</td>
	</tr>

	<tr valign="top">
	<th scope="row">Allow single page sharing</th>
	<td>
	<select name="p2c_show_used_pages">
	<option value="false"'.$p2c_defaults['show_usedpages_no'].' label=" No, thanks. "> No, thanks. </option>
	<option value="true"'.$p2c_defaults['show_usedpages_yes'].' label=" Yes, please! "> Yes, please! </option>
	</select> <small>(You can share a single page with more than a category, if you like.)</small>
	</td>
	</tr>

	</table>

	<h3><code>[catlist]</code> tag settings</h3>
	<p>You can use a <em>[catlist=xx]</em> tag in a post/page to show a list of post from a certain category, replacing <em>xx</em> with the category ID.</p>
	<table class="form-table">
	<tr valign="top">
	<th scope="row">Display at most </th>
	<td>
	<select name="p2c_catlist_limit">';

	$limits = array(1,2,3,4,5,6,7,8,9,10,15,20,25);
	foreach($limits as $limit):
		if(get_option('p2c_catlist_limit') == $limit) $flag = "selected='selected'"; else $flag = "";
		echo "<option value='".$limit."' name='opt".$limit."' ".$flag.">".$limit."</option>";
	endforeach;

	echo '</select> posts.
	</td>
	</tr>

	<tr valign="top">
	<th scope="row">Display list title</th>
	<td><input type="text" name="p2c_catlist_title" value="'.$p2c_catlist_title.'" size="30"/>
	<small>(Leave empty to show a post list without a title.)</small>
	</td>
	</tr>

	</table>
	';	

	echo '<p class="submit">
	<input type="hidden" name="page2cat_action" value="update" />
	<input type="submit" name="update" value="Update Settings" />
	</p>
	</fieldset>';
	
	echo '</form>';
	echo PIXLINE_FOOTER;
	echo "</div>";
}

// template function for manual hacks and widgets :-)
function page2cat_output($cat, $style = 'getpost', $useimg = 1){
global $wpdb;

	$useimg = get_option('p2c_use_img');
	$mypage = $wpdb->get_row("SELECT * FROM {$wpdb->page2cat} WHERE cat_ID = '".$cat."';",OBJECT);
#	print_r($mypage);
	$pageid = $mypage->page_ID;
	if($pageid != ""):
	
	switch($style):
	
	case 'getpost':
	?>
	<div id="category-page-header">
	<?php
	$post = $post_temp;
	$post = get_post($pageid);
	setup_postdata($post);
	
	if($post->post_title!=""){
				?>
				<div id="p2c-header">
					<h2><?php echo $post->post_title; ?></h2>
					<p><?php the_content(); ?></p>
				</div> 
		<div class="category-page-cleaner"></div>
	</div>
	<?php
	$post = $post_temp;
	}
	break;
	
	case 'widget':
	?>
	<div id="category-widget-header">
	<?php
	$pagina = "SELECT * FROM {$wpdb->posts} WHERE ID='".$pageid."' AND post_type = 'page';";
	$mine = $wpdb->get_results($pagina);
	if($mine[0]->post_title!=""){
				?>
				<div id="p2c-header">
					<!-- h2><?php echo $mine[0]->post_title; ?></h2 -->
					<p><?php echo wptexturize($mine[0]->post_content,1); ?></p>
				</div> 
		<div class="category-widget-cleaner"></div>
	</div>
	<?php
	}

	break;
	case 'inline':
	default:	
	?>
	<div id="category-page-header">
	<?php
	$pagina = "SELECT * FROM {$wpdb->posts} WHERE ID='".$pageid."' AND post_type = 'page';";
	$mine = $wpdb->get_results($pagina);
	if($mine[0]->post_title!=""){
				?>
				<div id="p2c-header">
					<h2><?php echo $mine[0]->post_title; ?></h2>
					<p><?php echo wptexturize($mine[0]->post_content,1); ?></p>
				</div> 
		<div class="category-page-cleaner"></div>
	</div>
	<?php
	}
	break;
	
	
	endswitch;
	
	?> 
	<?php
	else:
	echo '<h2 class="pagetitle">Archive for the &#8216;'.single_cat_title('',false).'&#8217; Category</h2>';	
	endif;
}


// adds sidebox in page write/edit
function page2cat_init(){
	if (function_exists('add_meta_box')) {
		add_meta_box('page2cat','Category Page Options', 'page2cat_add_meta_box', 'page');
		add_action('admin_head','page2cat_metabox_styles');
	} else {
		add_action('dbx_page_sidebar', 'page2cat_add_sidebox');
		add_action('admin_head','page2cat_sidebox_styles');
	}	
}
// install management and options page
function page2cat_config_page() {
	if ( function_exists('add_submenu_page') && is_admin()):
	add_submenu_page('edit.php', __('Category Pages'), __('Category Pages'), 8, 'category-pages', 'page2cat_manage_page');
	endif;
	if( function_exists('add_options_page') && is_admin()):
	add_options_page('Category Pages options','Category Pages',8,'category-page-options','page2cat_options_page');
	endif;
}


if( is_admin() ):
	if( preg_match('|page-new.php|i', $_SERVER['REQUEST_URI']) || 
		preg_match('|page.php|i', $_SERVER['REQUEST_URI']) || 
		preg_match('|edit.php|i', $_SERVER['REQUEST_URI']) ){
			add_action('admin_menu', 'page2cat_init');
	}
	add_action('admin_menu', 'page2cat_config_page');
	add_action('save_post','page2cat_trigger_save');
endif;

add_filter('the_content','page2cat_content_catlist');	// by popular demand :-)
?>
