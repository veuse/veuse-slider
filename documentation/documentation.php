<?php


// Set-up Action and Filter Hooks
add_action('admin_init', 'veuse_slideshow_documentation_init' );
add_action('admin_menu', 'veuse_slideshow_documentation_add_options_page');


// Init plugin options to white list our options
function veuse_slideshow_documentation_init(){
	register_setting( 'veuse_slideshow_documentation_plugin_options', 'veuse_slideshow_documentation_options', 'veuse_slideshow_documentation_validate_options' );
}


// Add menu page
function veuse_slideshow_documentation_add_options_page() {
	//add_submenu_page('Veuse Documentation Page', 'FAQ documentation', 'manage_options', 'slideshow_documentation', 'veuse_slideshow_documentation_render_form');
	add_submenu_page( 'edit.php?post_type=veuse_slider', __('Slider documentation page'), __('Slider documentation'), 'edit_themes', 'theme_options', 'veuse_slideshow_documentation_render_form');
}



function veuse_slideshow_documentation_render_form(){

	
    $theme_name = 'Veuse Slider'; 
	
	
	
	?>
	<style>
		#veuse-slideshow_documentation-wrapper a { text-decoration: none;}
		#veuse-slideshow_documentation-wrapper p {  }
		#veuse-slideshow_documentation-wrapper ul { margin-bottom: 30px !important;}
		ul.inline-list { list-style: disc !important; list-style-position: inside;}
		ul.inline-list li { display: inline; margin-right: 10px; list-style: disc;}
		ul.inline-list li:after { content:'-'; margin-left: 10px; }
	</style>
	<div class="wrap">

	
			
		<div id="veuse-documentation-wrapper" style="padding:20px 0; max-width:800px;">	

			<h1>Slideshow Documentation</h1>
			<h4>Here you find instructions on how to use the Veuse Slider plugin. For more in-depth info, please visit http://veuse.com/support.</h4>
			<hr>
			<br>
			<h3>Create a slideshow</h3>
			<ol>
				<li>Go to Slideshows &raquo; Add New Slideshow</li>
				<li>Give the slideshow a title</li>
				<li>Click on the Add Slide button to create a slide</li>
				<li>Add an image to the slide, and fill in the fields for caption, description etc.</li>
				<li>Repeat the above for all the slides you want in your slideshow</li>
				<li>Publish slideshow</li>
			</ol>
			
			<br><hr><br>
			
			<h3>Inserting slideshows on pages</h3>
			<p>You can insert your slider in two ways; Shortcode or via the Veuse Slider widget.
			
			<pre><code>[veuse_slider id="" width="" height="" speed="" slideshow="" animation="" controlnav="" directionnav=""]</code></pre>
				
			<h4>Attributes</h4>
			<ul>
				<li>id: The id of the slideshow you want to insert</li>
				<li>width: Width of slideshow ( Optional. Default: 1000)</li>
				<li>height: Height of slideshow ( Optional. Default: 500)</li>
				<li>slideshow: If the slideshow is to autoplay. True or false. Default: true</li>
				<li>animation: Slideshow transition-effect. slide or fade. Default: fade</li>
				<li>controlnav: Wether or not to show control-navigation. Default: true</li>
				<li>directionnav: Wether or not to show direction-arrows. Default: true</li>
			</ul>
		
		</div>
		
	</div>
	<?php
}
?>