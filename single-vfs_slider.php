
<?php get_header(); ?>

<div id="content" <?php post_class();?>>


<?php


/* ============================================================  */

/*
	POST HEADER

	Insert post header above content if set in
	child theme config.php

*/

	if( CEON_CONTENT_HEADER == 'outside'): // Defined in child theme config.php

			do_action('ceon_pagetitle_alpha');

				if(function_exists('veuse_locate_part')) :

				include_once(veuse_locate_part( $file = 'content-header', $dir = 'template-parts'));

				else:

				get_template_part('content', 'header');

				endif;

			do_action('ceon_pagetitle_omega');

	endif;


/* ============================================================  */ ?>


	<div class="row">


<?php

	$id = get_the_id();
	echo '<div class="small-12 large-12 columns">' . do_shortcode('[veuse_slider id="' . $id. '" width="1000" height="600"]') . '</div>';


/* ============================================================  */

/*
	PAGEBUILDER MODULES

	Loop through the post's selected modules,
	and inserts them on the post.

	The function is located in
	wp-content/plugins/veuse-pagebuilder/veuse-pagebuilder.php

*/

	if(function_exists('veuse_insert_modules'))

	do_action('veuse_insert_modules'); // Insert selected modules

	else

	get_template_part('content','single'); // Insert the content if pagebuilder is not installed

/* ============================================================  */



?>
	</div>
</div>
<?php get_footer();?>

