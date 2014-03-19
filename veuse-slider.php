<?php
/*
Plugin Name: Veuse Slider
Plugin URI: http://veuse.com/veuse-analytics
Description: Bild and insert a Flexslider on your website
Version: 1.0
Author: Andreas Wilthil
Author URI: http://veuse.com
License: GPL3
*/

__('Veuse Slider', 'veuse-slider' ); /* Dummy call for plugin name translation. */


class VeuseSlider{

	private $pluginURI  = '';
	private $pluginPATH = '';
	
	function __construct(){
		
		$this->pluginURI  = plugin_dir_url(__FILE__) ;
		$this->pluginPATH = plugin_dir_path(__FILE__) ;
		
		add_action('plugins_loaded', array(&$this,'veuse_slider_init'));
		
		add_action('init', array(&$this,'register_slider_posttype'));
		add_action('init', array(&$this,'veuse_slider_enqueue_script'), 10);
		add_action('admin_enqueue_scripts', array(&$this,'veuse_slider_admin_enqueue_script'), 1);
		
		add_action('media_buttons_context',  array(&$this,'add_my_custom_button'));
		add_action( 'admin_footer',  array(&$this,'slider_popup_content' ));
		
		add_shortcode('veuse_slider', array(&$this,'veuse_slider'));
		
		add_action('plugins_loaded', array(&$this,'load_textdomain'));
		
		//add_action( 'admin_menu', array(&$this,'remove_publish_box' ));
	}
	
	/* Localization
	============================================= */
	
	function load_textdomain() {
	    load_plugin_textdomain('veuse-slider', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
	
	function veuse_slider_init() {

		load_plugin_textdomain('veuse-flexslider', false, dirname(plugin_basename(__FILE__)) . '/languages');

    	require_once($this->pluginPATH . 'post-meta.php');
	}
	
	function veuse_slider_enqueue_script() {

		//wp_register_style( 'flexslider-css',  $this->pluginURI . 'assets/css/flexslider.css', array(), '', 'screen' );
	    //wp_enqueue_style ( 'flexslider-css' );
		
		wp_enqueue_script('flexslider-js', $this->pluginURI . 'assets/js/jquery.flexslider-min.js', array('jquery'), '', true);
		
	}
	
	
	function veuse_slider_admin_enqueue_script() {

		/* CSS */
		wp_register_style( 'slideshow-builder',  $this->pluginURI . 'assets/css/slideshow-builder.css', array(), '', 'screen' );
	    wp_enqueue_style ( 'slideshow-builder' );

		/* Javascript */
		if(function_exists( 'wp_enqueue_media' )){
			wp_enqueue_media();
		}
				
        wp_enqueue_script('media-upload');
        
        wp_enqueue_script('slideshow-builder', $this->pluginURI . 'assets/js/slideshow-builder.js', array('jquery'), '', true);
		
	}
	
	function remove_publish_box(){
		remove_meta_box( 'submitdiv', 'veuse_slider', 'side' );
	}

	
	function register_slider_posttype() {

		$labels = array(
	        'name' => __( 'Slideshows', 'veuse-flexslider' ), // Tip: _x('') is used for localization
	        'singular_name' => __( 'Slideshow', 'veuse-flexslider' ),
	        'add_new' => __( 'Add New Slideshow', 'veuse-flexslider' ),
	        'add_new_item' => __( 'Add New Slideshow','veuse-flexslider' ),
	        'edit_item' => __( 'Edit Slideshow', 'veuse-flexslider' ),
	        'all_items' => __( 'All Slideshows','veuse-flexslider' ),
	        'new_item' => __( 'New Slideshow','veuse-flexslider' ),
	        'view_item' => __( 'View Slideshow','veuse-flexslider' ),
	        'search_items' => __( 'Search Slideshows','veuse-flexslider' ),
	        'not_found' =>  __( 'No Slideshows','veuse-flexslider' ),
	        'not_found_in_trash' => __( 'No Slideshows found in Trash','veuse-flexslider' ),
	        'parent_item_colon' => ''
	    );

		register_post_type('veuse_slider',
					array(
					'labels' => $labels,
					'public' => true,
					'show_ui' => true,
					'_builtin' => false, // It's a custom post type, not built in
					'_edit_link' => 'post.php?post=%d',
					'capability_type' => 'post',
					'hierarchical' => false,
					'rewrite' => array("slug" => "slideshow"), // Permalinks
					'query_var' => "slider", // This goes to the WP_Query schema
					'supports' => array('title'),
					'menu_position' => 30,
					'menu_icon' => 'dashicons-images-alt2',
					'publicly_queryable' => true,
					'exclude_from_search' => false,
					'show_in_nav_menus' => false
					));
		}
		
		function veuse_slider( $atts, $content = null ) {

			 extract(shortcode_atts(array(
						'id' 			=> '',
						'width' 		=> '1000',
						'height' 		=> '500',
						'autoheight' 	=> 'true',
						'speed' 		=> 5000,
						'slideshow'		=> true,
						'animation' 	=> 'fade',
						'controlnav' 	=> true,
						'directionnav' 	=> true
	
			 ), $atts));
	
				ob_start();
				require($this->veuse_slider_locate_part('loop-veuse-slider'));
				$content = ob_get_contents();
				ob_end_clean();
		
				return $content;

		}



		function veuse_slider_columns( $columns ){

			$columns = array(
				"cb" => "<input type=\"checkbox\" />",
				"title" => "Slideshow Title",
				//"thumbnail" => "Featured image",
				//"exc" => "Excerpt",
				//"showcase" => "Showcase",
				"slides" => "Slides"
			);
		
			return $columns;
		
		}
			
		//add_filter("manage_edit-veuse_slider_columns", "veuse_slider_columns");
		
		function veuse_slider_locate_part($file) {

		     if ( file_exists( get_stylesheet_directory().'/'.$file.'.php'))
		     	$filepath = get_stylesheet_directory().'/'.$file.'.php';
			 else
		        $filepath = $this->pluginPATH .$file.'.php';
	
			return $filepath;
		}
		
		
		
		
		function add_my_custom_button($context) {

			  //path to my icon
			  $img = $this->pluginURI.'assets/images/icon-slider-large.png';
			
			  //our popup's title
			  $title = 'Slider';
			
			  //append the icon
			  $context .= "<a href='#TB_inline?&width=640&height=600&inlineId=veuse-slider-popup&modal=false' class='thickbox' title='{$title}' style='width:24px; margin:0; padding:0 !important;'>
		    <img src='{$img}' style='width:24px !important; height:24px !important; margin:-1px 0 0 2px;'/></a>";
			
			  return $context;
			}
			
		function slider_popup_content() {
			?>
			 <style>
			 
			 	#TB_overlay { z-index: 9998 !important; }
			 	#TB_window { z-index: 9999 !important; }
			 
			 	/* new clearfix */
				.clearfix:after {
					visibility: hidden;
					display: block;
					font-size: 0;
					content: " ";
					clear: both;
					height: 0;
					}
				* html .clearfix             { zoom: 1; } /* IE6 */
				*:first-child+html .clearfix { zoom: 1; } /* IE7 */

			 	div.info { width:45%; float: right; margin:0; padding:0;}
			 	div.selector {width:50%; float: left;}
			 	div.info p { margin:0 0 3px !important; padding:0 !important;}
			 	div.info p.desc { color: #888;}
			 	
			  	form#veuse-slider-insert { margin:0; width: auto; padding: 0; display: block;}
			  	form#veuse-slider-insert p { margin-bottom: 8px;}
			  	form#veuse-slider-insert hr { border:0; border-top:1px solid #eee !important; margin:15px 0; background-color: #eee !important;}
			  	form#veuse-slider-insert > section { margin-bottom: 10px; /*border-bottom: 1px dotted #d4d4d4;*/}
			  
			    .selector select,
			    .selector input[type=text] { width:100%;}			  	
			  	.selector ul { margin:0; } 
			  	.selector ul li { display: inline-block;  margin:0; padding:0;}	  	
			  	.selector ul li a{ color:#606060 !important; display: inline-block; padding:4px 8px; background:#eee;  border:1px solid #fff; text-decoration: none;
				  	
				  	border-radius: 2px;
				  	-moz-border-radius: 2px;
				  	-webkit-border-radius: 2px;
				  	margin:0 2px 2px 0;
				  	
			  	}
			  	
			  	.selector.group ul li a{ 
				  	
				  	border-radius: 0px;
				  	-moz-border-radius: 0px;
				  	-webkit-border-radius: 0px;
				  	margin:0 -5px 2px 0;
				  	
			  	}
			  	
			  	.selector.group ul li:first-child a {
			  			border-radius: 2px 0 0 2px;
				  	-moz-border-radius: 2px 0 0 2px;
				  	-webkit-border-radius: 2px 0 0 2px;
			  	}
			  	
			  	.selector.group ul li:lase-child a {
			  			border-radius: 0 2px 2px 0;
				  	-moz-border-radius: 0 2px 2px 0;
				  	-webkit-border-radius: 0 2px 2px 0;
			  	}
			  	
			  	.selector ul li a.active {   	
				  	background: #2a95c5; border-color:#fff; color:#fff !important;
			  	}
			  	
			  	
			
			  
			  </style>
			<div id="veuse-slider-popup" style="width:100%; height:100%; display:none;">
			  <h2><?php _e('Insert slideshow','veuse-slider');?></h2>
			  
			  <script>
			  
			  	jQuery(function($){
			  		
			  		jQuery('a.slider-selector-item').click(function(){
			  			$('#slider-selector a').removeClass('active');
			  			$(this).toggleClass('active');
			  			return false;
			  		});
			  		
			  		jQuery('#slider-autoplay-selector a').click(function(){
			  			$('#slider-autoplay-selector a').removeClass('active');
			  			$(this).addClass('active');
			  			return false;
			  		});
			  		
			  		jQuery('#slider-directionnav-selector a').click(function(){
			  			$('#slider-directionnav-selector a').removeClass('active');
			  			$(this).addClass('active');
			  			return false;
			  		});
			  		
			  		jQuery('#slider-controlnav-selector a').click(function(){
			  			$('#slider-controlnav-selector a').removeClass('active');
			  			$(this).addClass('active');
			  			return false;
			  		});
			  		
			  		


			  		 	  		
				  	jQuery('#insert-slider-shortcode').click(function(){
					  	
					  	var slidershortcode;					
					 						
						var slideshow = $('#slider-selector').val();	
						var height = $('#veuse-slider-height').val();
						var width = $('#veuse-slider-width').val();
						var speed = $('#veuse-slider-interval').val();
						
						var autoplay;
						if ($('#slider-autoplay-selector').find('a[data-id=true]').hasClass('active')){
							autoplay = 'true';
						} else {
							autoplay = 'false';
						}
						
						var controlnav;
						if ($('#slider-controlnav-selector').find('a[data-id=true]').hasClass('active')){
							controlnav = 'true';
						} else {
							controlnav = 'false';
						}
						
						var directionnav;
						if ($('#slider-directionnav-selector').find('a[data-id=true]').hasClass('active')){
							directionnav = 'true';
						} else {
							directionnav = 'false';
						}
						
						/*
						
						'autoheight' 	=> true,
						'interval' 		=> 5000,
						'autoplay' 		=> true,
						'animation' 	=> 'fade',
						'controlnav' 	=> true,
						'directionnav' 	=> true
						
						
						*/
							  		
					  	slidershortcode = '[veuse_slider id="' + slideshow + '" width="'+width+'" height="'+height+'" speed="'+speed+'" autoplay="'+autoplay+'" controlnav="'+controlnav+'" directionnav="'+directionnav+'" ]';
					  	tinyMCE.activeEditor.execCommand('mceInsertContent', false, slidershortcode);
					    tb_remove();
					  	return false;
				  	});
				  	
			  	});
			  
			  
			  
			  </script>
			  
			  
			  <form id="veuse-slider-insert" class="clearfix">
			 
				<hr>
			  	 <section class="clearfix">
					<div class="info">
						<p><strong><?php _e('Sliders','veuse-slider');?></strong></p>
						<p class="desc">Select slideshow to display</p>
					</div>
					<div class="selector">	
						 
						 <select id="slider-selector">
						 
			 			<?php $args = array(
								'posts_per_page'   => -1,
								'offset'           => 0,
								'orderby'          => 'post_date',
								'order'            => 'DESC',
								'post_type'        => 'veuse_slider',
								'post_status'      => 'publish',
								'suppress_filters' => true ); 
								
								
								$slideshows = get_posts($args);
								
								foreach($slideshows as $slideshow){
								
									echo '<option value="'.$slideshow->ID.'">'.$slideshow->post_title.'</option>';
								}
											
							?>
						 </select>
					 </div>
			  	</section>
				<hr>	
				
				<section class="clearfix">
					<div class="info">
						<p><strong><?php _e('Width','veuse-portfolio');?></strong></p>
						<p class="desc"><?php _e('Override the predefined slider width','veuse-portfolio');?></p>
					</div>
					<div class="selector">
					<input type="text" name="veuse-slider-width" id="veuse-slider-width" value="1000" />
					</div>
				</section>
				
			  	
			  	<hr>
			  	
			  	<section class="clearfix">
					<div class="info">
						<p><strong><?php _e('Height','veuse-portfolio');?></strong></p>
						<p class="desc"><?php _e('Override the predefined slider height','veuse-portfolio');?></p>
					</div>
					<div class="selector">
					<input type="text" name="veuse-slider-height" id="veuse-slider-height" value="500" />
					</div>
				</section>
				
				<hr>
				
				<section class="clearfix">
					<div class="info">
						<p><strong><?php _e('Interval','veuse-portfolio');?></strong></p>
						<p class="desc"><?php _e('Time between each slide in milliseconds. To turn off autoplay, enter 0','veuse-portfolio');?></p>
					</div>
					<div class="selector">
					<input type="text" name="veuse-slider-interval" id="veuse-slider-interval" value="7000"/>
					</div>
				</section>
			  	
			  	<hr>
			  	
			  	 <section class="clearfix">
					<div class="info">
					<p><strong><?php _e('Autoplay','veuse-portfolio');?></strong></p>
					<p class="desc">Select if you want the slideshow to run automatically.</p>
					</div>
					<div class="selector group">	
						<ul id="slider-autoplay-selector" class="clearfix">				
							<li><a href="#" class="slider-autoplay-selector-item active" data-id="true">Yes</a></li>
							<li><a href="#" class="slider-autoplay-selector-item" data-id="false">No</a></li>
						</ul>
					</div>
			  	</section>
			  	
			  	<hr>
			  	
			  	<section class="clearfix">
					<div class="info">
					<p><strong><?php _e('Show directional navigation','veuse-portfolio');?></strong></p>
					
					</div>
					<div class="selector group">	
						<ul id="slider-directionnav-selector" class="clearfix">				
							<li><a href="#" class="slider-directionnav-selector-item active" data-id="true">Yes</a></li>
							<li><a href="#" class="slider-directionnav-selector-item" data-id="false">No</a></li>
						</ul>
					</div>
			  	</section>
			  	
			  	<hr>
			  	
			  	<section class="clearfix">
					<div class="info">
					<p><strong><?php _e('Show controls navigation','veuse-portfolio');?></strong></p>
					
					</div>
					<div class="selector group">	
						<ul id="slider-controlnav-selector" class="clearfix">				
							<li><a href="#" class="slider-controlnav-selector-item active" data-id="true">Yes</a></li>
							<li><a href="#" class="slider-controlnav-selector-item" data-id="false">No</a></li>
						</ul>
					</div>
			  	</section>
			  	
			  	<hr>
			  				  		
				<input type="submit" class="button-primary" id="insert-slider-shortcode"  value="<?php _e('Insert shortcode') ?>" />	  
			  
			  </form>
			</div>
			<?php
			}




	
}

$veuse_slider = new VeuseSlider;


require 'widget.php'; 


/* Insert retina image */

if(!function_exists('veuse_retina_interchange_image')){

	function veuse_retina_interchange_image($img_url, $width, $height, $crop){

		$imagepath = '<img src="'. mr_image_resize($img_url, $width, $height, $crop, 'c', false) .'" data-interchange="['. mr_image_resize($img_url, $width, $height, $crop, 'c', true) .', (retina)]" alt="" />';
	
		return $imagepath;
	
	}
}





/**
  *  Resizes an image and returns the resized URL. Uses native WordPress functionality.
  *
  *  The function supports GD Library and ImageMagick. WordPress will pick whichever is most appropriate.
  *  If none of the supported libraries are available, the function will return the original image url.
  *
  *  Images are saved to the WordPress uploads directory, just like images uploaded through the Media Library.
  * 
  *  Supports WordPress 3.5 and above.
  * 
  *  Based on resize.php by Matthew Ruddy (GPLv2 Licensed, Copyright (c) 2012, 2013)
  *  https://github.com/MatthewRuddy/Wordpress-Timthumb-alternative
  * 
  *  License: GPLv2
  *  http://www.gnu.org/licenses/gpl-2.0.html
  *
  *  @author Ernesto MŽndez (http://der-design.com)
  *  @author Matthew Ruddy (http://rivaslider.com)
  */

if(!function_exists('mr_image_resize')){

	add_action('delete_attachment', 'mr_delete_resized_images');
	
	function mr_image_resize($url, $width=null, $height=null, $crop=true, $align='c', $retina=false) {
	
	  global $wpdb;
	
	  // Get common vars
	  $args = func_get_args();
	  $common = mr_common_info($args);
	
	  // Unpack vars if got an array...
	  if (is_array($common)) extract($common);
	
	  // ... Otherwise, return error, null or image
	  else return $common;
	
	  if (!file_exists($dest_file_name)) {
	
	    // We only want to resize Media Library images, so we can be sure they get deleted correctly when appropriate.
	    $query = $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE guid='%s'", $url);
	    $get_attachment = $wpdb->get_results($query);
	
	    // Load WordPress Image Editor
	    $editor = wp_get_image_editor($file_path);
	    
	    // Print possible wp error
	    if (is_wp_error($editor)) {
	      if (is_user_logged_in()) print_r($editor);
	      return null;
	    }
	
	    if ($crop) {
	
	      $src_x = $src_y = 0;
	      $src_w = $orig_width;
	      $src_h = $orig_height;
	
	      $cmp_x = $orig_width / $dest_width;
	      $cmp_y = $orig_height / $dest_height;
	
	      // Calculate x or y coordinate and width or height of source
	      if ($cmp_x > $cmp_y) {
	
	        $src_w = round ($orig_width / $cmp_x * $cmp_y);
	        $src_x = round (($orig_width - ($orig_width / $cmp_x * $cmp_y)) / 2);
	
	      } else if ($cmp_y > $cmp_x) {
	
	        $src_h = round ($orig_height / $cmp_y * $cmp_x);
	        $src_y = round (($orig_height - ($orig_height / $cmp_y * $cmp_x)) / 2);
	
	      }
	
	      // Positional cropping. Uses code from timthumb.php under the GPL
	      if ($align && $align != 'c') {
	        if (strpos ($align, 't') !== false) {
	          $src_y = 0;
	        }
	        if (strpos ($align, 'b') !== false) {
	          $src_y = $orig_height - $src_h;
	        }
	        if (strpos ($align, 'l') !== false) {
	          $src_x = 0;
	        }
	        if (strpos ($align, 'r') !== false) {
	          $src_x = $orig_width - $src_w;
	        }
	      }
	      
	      // Crop image
	      $editor->crop($src_x, $src_y, $src_w, $src_h, $dest_width, $dest_height);
	
	    } else {
	     
	      // Just resize image
	      $editor->resize($dest_width, $dest_height);
	     
	    }
	
	    // Save image
	    $saved = $editor->save($dest_file_name);
	    
	    // Print possible out of memory error
	    if (is_wp_error($saved)) {
	      @unlink($dest_file_name);
	      if (is_user_logged_in()) print_r($saved);
	      return null;
	    }
	
	    // Add the resized dimensions and alignment to original image metadata, so the images
	    // can be deleted when the original image is delete from the Media Library.
	    if ($get_attachment) {
	      $metadata = wp_get_attachment_metadata($get_attachment[0]->ID);
	      if (isset($metadata['image_meta'])) {
	        $md = $saved['width'] . 'x' . $saved['height'];
	        if ($crop) $md .= ($align) ? "_${align}" : "_c";
	        $metadata['image_meta']['resized_images'][] = $md;
	        wp_update_attachment_metadata($get_attachment[0]->ID, $metadata);
	      }
	    }
	
	    // Resized image url
	    $resized_url = str_replace(basename($url), basename($saved['path']), $url);
	
	  } else {
	
	    // Resized image url
	    $resized_url = str_replace(basename($url), basename($dest_file_name), $url);
	
	  }
	
	  // Return resized url
	  return $resized_url;
	
	}
	
	// Returns common information shared by processing functions
	
	function mr_common_info($args) {
	
	  // Unpack arguments
	  list($url, $width, $height, $crop, $align, $retina) = $args;
	  
	  // Return null if url empty
	  if (empty($url)) {
	    return is_user_logged_in() ? "image_not_specified" : null;
	  }
	
	  // Return if nocrop is set on query string
	  if (preg_match('/(\?|&)nocrop/', $url)) {
	    return $url;
	  }
	  
	  // Get the image file path
	  $urlinfo = parse_url($url);
	  $wp_upload_dir = wp_upload_dir();
	  
	  if (preg_match('/\/[0-9]{4}\/[0-9]{2}\/.+$/', $urlinfo['path'], $matches)) {
	    $file_path = $wp_upload_dir['basedir'] . $matches[0];
	  } else {
	    return $url;
	  }
	  
	  // Don't process a file that doesn't exist
	  if (!file_exists($file_path)) {
	    return null; // Degrade gracefully
	  }
	  
	  // Get original image size
	  $size = @getimagesize($file_path);
	
	  // If no size data obtained, return error or null
	  if (!$size) {
	    return is_user_logged_in() ? "getimagesize_error_common" : null;
	  }
	
	  // Set original width and height
	  list($orig_width, $orig_height, $orig_type) = $size;
	
	  // Generate width or height if not provided
	  if ($width && !$height) {
	    $height = floor ($orig_height * ($width / $orig_width));
	  } else if ($height && !$width) {
	    $width = floor ($orig_width * ($height / $orig_height));
	  } else if (!$width && !$height) {
	    return $url; // Return original url if no width/height provided
	  }
	
	  // Allow for different retina sizes
	  $retina = $retina ? ($retina === true ? 2 : $retina) : 1;
	
	  // Destination width and height variables
	  $dest_width = $width * $retina;
	  $dest_height = $height * $retina;
	
	  // Some additional info about the image
	  $info = pathinfo($file_path);
	  $dir = $info['dirname'];
	  $ext = $info['extension'];
	  $name = wp_basename($file_path, ".$ext");
	
	  // Suffix applied to filename
	  $suffix = "${dest_width}x${dest_height}";
	
	  // Set align info on file
	  if ($crop) {
	    $suffix .= ($align) ? "_${align}" : "_c";
	  }
	
	  // Get the destination file name
	  $dest_file_name = "${dir}/${name}-${suffix}.${ext}";
	  
	  // Return info
	  return array(
	    'dir' => $dir,
	    'name' => $name,
	    'ext' => $ext,
	    'suffix' => $suffix,
	    'orig_width' => $orig_width,
	    'orig_height' => $orig_height,
	    'orig_type' => $orig_type,
	    'dest_width' => $dest_width,
	    'dest_height' => $dest_height,
	    'file_path' => $file_path,
	    'dest_file_name' => $dest_file_name,
	  );
	
	}
	
	// Deletes the resized images when the original image is deleted from the WordPress Media Library.
	
	function mr_delete_resized_images($post_id) {
	
	  // Get attachment image metadata
	  $metadata = wp_get_attachment_metadata($post_id);
	  
	  // Return if no metadata is found
	  if (!$metadata) return;
	
	  // Return if we don't have the proper metadata
	  if (!isset($metadata['file']) || !isset($metadata['image_meta']['resized_images'])) return;
	  
	  $wp_upload_dir = wp_upload_dir();
	  $pathinfo = pathinfo($metadata['file']);
	  $resized_images = $metadata['image_meta']['resized_images'];
	  
	  // Delete the resized images
	  foreach ($resized_images as $dims) {
	
	    // Get the resized images filename
	    $file = $wp_upload_dir['basedir'] . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $dims . '.' . $pathinfo['extension'];
	
	    // Delete the resized image
	    @unlink($file);
	
		}
    }
}


?>