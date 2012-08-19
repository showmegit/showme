<?php get_header(); ?>

	<div id="content" class="narrowcolumn" role="main">

	<?php if (have_posts()) : ?>

		<h2 class="pagetitle">Search Results</h2>

		<?php while (have_posts()) : the_post(); ?>
			<div class="post box" id="post-<?php the_ID(); ?>">
				<div class="box-t">
					<div class="box-b">

						<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
						<small class="date"><?php the_time('F jS, Y') ?> <!-- by <?php the_author() ?> --></small>

						<div class="entry">
							<?php the_content('Read more &raquo;'); ?>
						</div>

						<div class="post-meta">
							<div class="post-meta-b">
								<div class="cl">&nbsp;</div>
								<div class="left">
									<p>Posted in <?php the_category(', ') ?></p>
									<?php the_tags('<p>Tags: ', ', ', '</p>'); ?>
								</div>
								<div class="right">
									<p><?php comments_popup_link('0 Comments', '1 Comment', '% Comments'); ?></p>
									<!-- <p><?php edit_post_link('Edit', '', ''); ?></p> -->
								</div>
								
								<div class="cl">&nbsp;</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endwhile; ?>

		<div class="page-nav">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">No posts found. Try a different search?</h2>
		<?php get_search_form(); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>