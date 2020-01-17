<?php
/**
 * 
 * Liste des actualités
 * 
 */  

// page en cours (pager)
$current_page_number = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

/*=============================
=            Query            =
=============================*/

$news_query_args = array(
    'post_type' => NEWS_POST_SLUG,
    'posts_per_page' => get_option( 'posts_per_page' ),
    'paged' => $current_page_number
);

if ( get_query_var( NEWS_TAX_QUERY_VAR ) ) {
    $news_query_args['tax_query'] = array(
        array (
            'taxonomy' => NEWS_TAX_SLUG,
            'field' => 'slug',
            'terms' => get_query_var( NEWS_TAX_QUERY_VAR )
        )
    );
}

$news_query = new WP_Query( $news_query_args );


/*=====  FIN Query  =====*/

/*=================================
=            Affichage            =
=================================*/

if ( $news_query->have_posts() ) { ?>

    <div class="st-list">

    <?php while ( $news_query->have_posts() ) { $news_query->the_post();

        pc_display_post_resum( $post->ID, '', 2, true, true );

    } ?>

    </div>

    <?php pc_get_pager( $news_query, $current_page_number );
    
}
 
 
 /*=====  FIN Affichage  =====*/
 
 // reset query
 wp_reset_postdata();