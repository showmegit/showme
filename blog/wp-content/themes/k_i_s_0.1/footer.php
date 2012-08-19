		<div class="cl">&nbsp;</div>
	</div>
	<div id="footer" class="nav">
		<ul>
			<li <?php if(is_home()) echo 'class="current_page_item"'; ?>><a href="<?php echo get_option('home'); ?>/" >Home</a></li>
			<?php wp_list_pages('title_li=&parent=0'); ?>
		</ul>
		<p class="copy">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Design by <a href="http://cssmayo.com">cssmayo.com</a>, Powered by <a href="http://wordpress.org" rel="nofollow">WordPress</a></p>
		<div class="cl">&nbsp;</div>
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
