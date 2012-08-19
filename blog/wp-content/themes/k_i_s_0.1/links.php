<?php
/*
Template Name: Links
*/
?>
<?php get_header(); ?>
	<div id="content" class="narrowcolumn" role="main">
		<div class="post box">
			<div class="box-t">
				<div class="box-b">
					<h2>Links:</h2>
					<ul><?php wp_list_bookmarks(); ?></ul>
				</div>
			</div>
		</div>
	</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
