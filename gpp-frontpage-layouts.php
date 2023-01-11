<?php
/*
Plugin Name: GPP Frontpage Layouts
Description: Different style of post layouts suitable for the frontpage.
Author: Geraldo Pena Perez
Version: 1.0.0
*/

if(!defined('ABSPATH')){
	exit;
}

function gpp_fp_layouts_img_size() {
	add_theme_support('post-thumbnails');
	add_image_size('gpp-fp-layouts-400x230', 400, 230, true);
}
add_action('init', 'gpp_fp_layouts_img_size');

function gpp_fp_block_load_block_files() {
	wp_enqueue_script(
		'gpp-fp-layouts',
		plugin_dir_url(__FILE__) . 'gpp-fp-block.js',
		array('wp-blocks', 'wp-i18n', 'wp-editor'),
		'0.1',
		true
	);
	
	$categories = array();
	$cats = get_categories();

	foreach ( $cats as $cat ) {
		$categories[] = array(
			'label' => $cat->name,
			'value' => $cat->term_id,
		);
	}
	
	$layouts = 4; //Drop down list of layouts
	
	wp_localize_script(
		'gpp-fp-layouts',
		'gpp_fp_layouts',
		array(
			'categories'  => $categories,
			'layouts' => $layouts
		)
	);
	
	register_block_type(
		'gpp/fp-layouts',
		array(
			'editor_script' => 'gpp-fp-layouts',
			'render_callback' => 'gpp_fp_frontend_render_callback',
			'attributes' => array(
				'title' => array(
					'type' => 'string',
					'default' => ''
				),
				'category' => array(
					'type' => 'array',
					'default' => ''					
				),
				'layout' => array(
					'type' => 'number',
					'default' => 1					
				),
				'header_color' => array(
					'type' => 'string',
					'default' => '#0670B7'					
				),
				'offset' => array(
					'type' => 'number',
					'default' => 0					
				),
				'white_text' => array(
					'type' => 'boolean',
					'default' => false					
				)
			)
		)
	);
}
add_action('init', 'gpp_fp_block_load_block_files', 11);

function gpp_fp_register_style() {
	wp_register_style(
		'gpp-fp-layouts-style',
		plugin_dir_url( __FILE__ ) . 'style.css',
		array(),
		'0.1'
	);
}
add_action('init', 'gpp_fp_register_style' );

function gpp_fp_enqueue_block_assets() {
	if (is_admin()){
		wp_enqueue_style('gpp-fp-layouts-style');
	}
	if (is_singular()){
		$id = get_the_ID();
		if(has_block('gpp/fp-layouts', $id)){
			wp_enqueue_style('gpp-fp-layouts-style');
		}
	}
}
add_action('enqueue_block_assets', 'gpp_fp_enqueue_block_assets');

function gpp_fp_frontend_render_callback($attributes){
	$white_text = ($attributes['white_text'])?' color: #ffffff !important;':'';
	$display = '<section class="widget widget_gpp-frontpage-layouts">';
	if(!empty($attributes['title']) && $attributes['layout'] != 3){
		$display .= '<h2 class="widget-title" style="background-color: '.$attributes['header_color'].';'.$white_text.'">'.$attributes['title'].'</h2>';
	}
	
	$display_body = '<h2 class="gpp-fp-layouts-no-cat">Please select the category</h2>';
	
	if(!empty($attributes['category'])){
	
		$query_args = array(
			'category__in' => $attributes['category'],
			'posts_per_page' => 13, //The biggest number of posts in a layout.
			'offset' => $attributes['offset'],
			'post-type' => 'post',
			'no_found_rows' => true
		);
		
		$my_query = new WP_Query();
		$my_query->query($query_args);
		
		$display_body = '<div class="gpp-fp-layouts-main">';
		
		switch($attributes['layout']){
			case 1:
				if($my_query->have_posts()){
					$my_query->the_post();
					$display_body .= '<div class="gpp-fp-layouts-2cols layout-1">';
					$display_body .= '<div>';
					if(has_post_thumbnail()){
						$post_id = get_the_ID();
						$thumb_id = get_post_thumbnail_id($post_id);
						$thumb_url = get_the_post_thumbnail_url($post_id, 'gpp-fp-layouts-400x230');
						$thumb_alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
						$display_body .= '<span class="center"><a href="'.get_permalink().'"><img class="large-thumb gpp-no-lazy" src="'.$thumb_url.'" alt="'.$thumb_alt.'" width="400" height="230" /></a></span>';
					}
					$display_body .= '<small class="date-meta">'.get_the_date().'</small>';
					$display_body .= '<h3><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
					$display_body .= '<p>'.get_the_excerpt().'</p>';
					$display_body .= '</div>';
					$display_body .= '<div class="flex-col">';
				}
				for($i = 0; $i < 4; $i++){
					if($my_query->have_posts()){
						$my_query->the_post();
						$display_body .= '<div class="l1-right-col-item">';
						$display_body .= '<div>';
						if(has_post_thumbnail()){
							$post_id = get_the_ID();
							$thumb_id = get_post_thumbnail_id($post_id);
							$thumb_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
							$thumb_alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
							$display_body .= '<a href="'.get_permalink().'"><img src="'.$thumb_url.'" class="gpp-no-lazy" alt="'.$thumb_alt.'" width="80" height="80" /></a>';
						}
						$display_body .= '</div>';
						$display_body .= '<div class="l1-list-details">';
						$display_body .= '<h3><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
						$display_body .= '<small class="date-meta">'.get_the_date().'</small>';
						$display_body .= '</div>';
						$display_body .= '</div>';
					}else{
						break;
					}
				}
				$display_body .= '</div>';
				break;
			case 2:
				$display_body .= '<div class="gpp-fp-layouts-2cols layout-2">';
				if($my_query->have_posts()){
					$my_query->the_post();
					$display_body .= '<div>';
					if(has_post_thumbnail()){
						$post_id = get_the_ID();
						$thumb_id = get_post_thumbnail_id($post_id);
						$thumb_url = get_the_post_thumbnail_url($post_id, 'gpp-fp-layouts-400x230');
						$thumb_alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
						$display_body .= '<span class="center"><a href="'.get_permalink().'"><img class="large-thumb" src="'.$thumb_url.'" alt="'.$thumb_alt.'" width="400" height="230" /></a></span>';
					}
					$display_body .= '<small class="date-meta">'.get_the_date().'</small>';
					$display_body .= '<h3><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
					$display_body .= '<p>'.get_the_excerpt().'</p>';
					$display_body .= '</div>';
				}
				if($my_query->have_posts()){
					$my_query->the_post();
					$display_body .= '<div>';
					if(has_post_thumbnail()){
						$post_id = get_the_ID();
						$thumb_id = get_post_thumbnail_id($post_id);
						$thumb_url = get_the_post_thumbnail_url($post_id, 'gpp-fp-layouts-400x230');
						$thumb_alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
						$display_body .= '<span class="center"><a href="'.get_permalink().'"><img class="large-thumb" src="'.$thumb_url.'" alt="'.$thumb_alt.'" width="400" height="230" /></a></span>';
					}
					$display_body .= '<small class="date-meta">'.get_the_date().'</small>';
					$display_body .= '<h3><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
					$display_body .= '<p>'.get_the_excerpt().'</p>';
					$display_body .= '</div>';
				}
				$display_body .= '</div>';
				break;
			case 3:
				if(!empty($attributes['title'])){
					$display_body .= '<h4 class="l3-widget-title">'.$attributes['title'].'</h4>';
				}
				$display_body .= '<ul class="l3-structure">';
				for($i = 0; $i < 8; $i++){
					if($my_query->have_posts()){
						$my_query->the_post();
						$display_body .= '<li>';
						$display_body .= '<p><a href="'.get_permalink().'">'.get_the_title().'</a></p>';
						$display_body .= '</li>';
					}else{
						break;
					}
				}
				$display_body .= '</ul>';
				break;
			case 4:
				$display_body .= '<div class="gpp-fp-layouts-2cols layout-4">';
				$display_body .= '<div class="flex-col">';
				for($i = 0; $i < 3; $i++){
					if($my_query->have_posts()){
						$my_query->the_post();
						$display_body .= '<div class="l1-right-col-item">';
						$display_body .= '<div>';
						if(has_post_thumbnail()){
							$post_id = get_the_ID();
							$thumb_id = get_post_thumbnail_id($post_id);
							$thumb_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
							$thumb_alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
							$display_body .= '<a href="'.get_permalink().'"><img class="large-thumb" src="'.$thumb_url.'" alt="'.$thumb_alt.'" width="80" height="80" /></a>';
						}
						$display_body .= '</div>';
						$display_body .= '<div class="l1-list-details">';
						$display_body .= '<h3><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
						$display_body .= '<small class="date-meta">'.get_the_date().'</small>';
						$display_body .= '</div>';
						$display_body .= '</div>';
					}else{
						break;
					}
				}
				$display_body .= '</div>';
				$display_body .= '<div class="flex-col">';
				for($i = 0; $i < 3; $i++){
					if($my_query->have_posts()){
						$my_query->the_post();
						$display_body .= '<div class="l1-right-col-item layout-4">';
						$display_body .= '<div>';
						if(has_post_thumbnail()){
							$post_id = get_the_ID();
							$thumb_id = get_post_thumbnail_id($post_id);
							$thumb_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
							$thumb_alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
							$display_body .= '<a href="'.get_permalink().'"><img class="large-thumb" src="'.$thumb_url.'" alt="'.$thumb_alt.'" width="80" height="80" /></a>';
						}
						$display_body .= '</div>';
						$display_body .= '<div class="l1-list-details">';
						$display_body .= '<h3><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
						$display_body .= '<small class="date-meta">'.get_the_date().'</small>';
						$display_body .= '</div>';
						$display_body .= '</div>';
					}else{
						break;
					}
				}
				$display_body .= '</div>';
				break;
		}
		
		$display_body .= '</div>';
		wp_reset_postdata();
	}
	
	$display .= $display_body;
	$display .= '</section>';
	
	return $display;
}
