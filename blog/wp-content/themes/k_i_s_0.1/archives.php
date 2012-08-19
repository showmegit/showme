<?php
/*
Template Name: Archives
*/
?>
<?php get_header(); ?>
	<div id="content" class="narrowcolumn" role="main">
		<div class="post box">
			<div class="box-t">
				<div class="box-b">
					<?php get_search_form(); ?>

					<h2>Archives by Month:</h2>
					<ul><?php wp_get_archives('type=monthly'); ?></ul>

					<h2>Archives by Subject:</h2>
					<ul><?php wp_list_categories(); ?></ul>
				</div>
			</div>
		</div>
	</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
