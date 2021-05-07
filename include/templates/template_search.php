<?php 
/**
 * 
 * Template : résultats de la recherche
 * 
 */

/*=============================
=            Hooks            =
=============================*/

// main start
add_action( 'pc_action_search_main_start', 'pc_display_main_start', 10 ); // template-part_layout.php

	// header
	add_action( 'pc_action_search_main_header', 'pc_display_main_header_start', 10 ); // template-part_layout.php
		add_action( 'pc_action_search_main_header', 'pc_display_breadcrumb', 20 ); // breadcrumb
		add_action( 'pc_action_search_main_header', 'pc_display_search_main_title', 30 ); // titre
	add_action( 'pc_action_search_main_header', 'pc_display_main_header_end', 100 ); // template-part_layout.php

	// content
	add_action( 'pc_action_search_main_content', 'pc_display_main_content_start', 10 ); // template-part_layout.php
		add_action( 'pc_action_search_main_content', 'pc_display_search_results', 30 ); // résultats
	add_action( 'pc_action_search_main_content', 'pc_display_main_content_end', 100 ); // template-part_layout.php

	// footer
	add_action( 'pc_action_search_main_footer', 'pc_display_search_footer', 10 ); // template-part_layout.php

// main end
add_action( 'pc_action_search_main_end', 'pc_display_main_end', 10 ); // template-part_layout.php




/*=====  FIN Hooks  =====*/

function pc_display_search_main_title() {

	echo apply_filters( 'pc_filter_search_main_title', '<h1><span>Recherche</span></h1>' );

}

function pc_display_search_results() {
		
	global $wp_query;
	$count = $wp_query->found_posts;
	$txt = ( $count > 1 ) ? 'résultats' : 'résultat';

	$pages_count = ceil( $count / get_option( 'posts_per_page' ) );
	$pages_count_txt = ( $pages_count > 1 ) ? ' sur <strong>'.$pages_count.' pages</strong>' : '';

	$types = apply_filters( 'pc_filter_search_results_type', array(
		'page' => 'Page'
	) );

	pc_display_form_search();
	echo '<p class="s-results-infos"><strong>'.$count.'</strong> '.$txt.$pages_count_txt.'.</p>';

	echo '<ol class="s-results-list reset-list">';

	foreach ( $wp_query->posts as $post ) {
		
		$pc_post = new PC_Post( $post );
		$tag = ( array_key_exists( $pc_post->type, $types ) ) ? $types[$pc_post->type] : '';

		echo '<li class="s-results-item s-results-item--'.$pc_post->type.'">';
			echo '<h2 class="s-results-item-title"><a class="s-results-item-link" href="'.$pc_post->permalink.'" title="Lire la suite">'.$pc_post->get_card_title().' <span>'.$tag.'</span></a></h2>';
			echo '<p class="s-results-item-desc">'.$pc_post->get_card_description().'&nbsp;<span class="st-desc-ico">'.pc_svg('arrow').'</span></p>';
		echo '</li>';

	}
	
	echo '</ol>';

}

function pc_display_search_footer() {

	global $wp_query;
	$count = $wp_query->found_posts;

	if ( $count > get_option( 'posts_per_page' ) ) {
		
		pc_display_main_footer_start();
			pc_get_pager();
		pc_display_main_footer_end();

	}

}