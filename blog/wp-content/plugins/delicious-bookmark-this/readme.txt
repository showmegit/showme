=== del.icio.us - Bookmark this! ===
Tags: delicious, bookmarks, social sharing
Contributors: arnee
Donate link: http://www.arnebrachhold.de/redir/paypal/
Stable tag: trunk

This plugin will allow you to add an "Bookmark this page on del.icio.us/digg/furl/newsvine/blinklist" link on you sidebar / posts / wherever.

== Installation ==

1. Upload the full directory into your wp-content/plugins directory
2. Activate it in the Plugin options
3. Put the following code in your template where you want to place the bookmark link: 
   `<?php dbt_getLinkTag("Bookmark on del.icio.us"); ?>` This will bookmark the current page viewed in browser to del.icio.us.
   If you want to bookmark the CURRENT POST, use <?php dbt_the_LinkTag("Bookmark on del.icio.us"); ?> inside "the loop".
   "The loop" can be found in your home template (index.php) and in your post template (single.php). Search
   for `<?php if (have_posts()) : while (have_posts()) : the_post(); ?>` and place it after it, but before `<?php endwhile; else: ?>`
   which should be near the end of your template file. 
   More information about the loop can be found at http://codex.wordpress.org/The_Loop
4. The first parameter of the function is the text of the link.
   The second parameter is the service which you want to use. Currently supported are: "delicios","digg","furl","newsvine" and "blinklist"
   The third and last paramter is a string which may contain additional attributes for the link tag, "class='link'" to define a CSS class for example.
5. Samples:

   * `<?php dbt_getLinkTag("Bookmark on del.icio.us"); ?>`			Normal del.icio.us link (bookmarks current page)
   * `<?php dbt_getLinkTag("Bookmark on furl","furl"); ?>`			Normal furl.net link (bookmarks current page)
   
   * `<?php dbt_the_LinkTag("Bookmark on del.icio.us"); ?>`			Normal del.icio.us link (bookmarks current POST, use inside the loop!)
   * `<?php dbt_the_LinkTag("Bookmark on digg","digg"); ?>`			Normal digg link (bookmarks current POST, use inside the loop!)
   
   * `<?php dbt_getLinkTag("<img src='blink.gif' />","blinklist"); ?>`		Link with image to blinklist (bookmarks current page)
   * `<?php dbt_the_LinkTag("<img src='vine.gif' />","newsvine"); ?>`		Link with image to newsvine (bookmarks current POST, use inside the loop!)
   
== Frequently Asked Questions == 

= Can I use an image as the link? =

Yes you can! Just use the image tag as the text. Example: <?php dbt_getLinkTag("<img src='book.gif' border='0' />"); ?>

= Can I specify a CSS class for the link? =

Yes you can! Just supply a second parameter to the function. Example: <?php dbt_getLinkTag("Bookmark on del.icio.us","delicious""class='link'"); ?>

= Can I use more than one bookmark service? =

Yes youn can! Just repeat the function several times. Example <?php dbt_the_LinkTag("Bookmark on del.icio.us","delicious"); ?> <?php dbt_the_LinkTag("Bookmark on furl","furl"); ?>

= Which bookmark services are supported? =

There is currently support for:

* "delicious" (http://del.icio.us)
* "digg" (http://www.digg.com)
* "furl" (http://www.furl.net)
* "newsvine" (http://www.newsvine.com) 
* "blinklist" (http://www.blinklist.com)

Use the quoted string as the second parameter for the functions explained above.
