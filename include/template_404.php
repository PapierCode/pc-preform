<?php 
/**
 * 
 * Template 404
 * 
 */


add_action( 'pc_404_content_before', 'pc_display_main_start', 10 ); // layout commun -> fn-template_layout.php
add_action( 'pc_404_content_after', 'pc_display_main_end', 10 ); // layout commun -> fn-template_layout.php
