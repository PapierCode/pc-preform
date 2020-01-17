<?php
/**
 * 
 * Gestion des images
 * 
 */


/*===============================
=            Tailles            =
===============================*/

add_action( 'after_switch_theme', 'pc_define_default_img_sizes' );

function pc_define_default_img_sizes() {

    // update_option( 'thumbnail_crop', 0 );
    // update_option( 'thumbnail_size_h', 150 );
    // update_option( 'thumbnail_size_w', 150 );
    // update_option( 'medium_size_h', 150 );
    // update_option( 'medium_size_w', 150 );
    // update_option( 'medium_large_size_h', 150 );
    // update_option( 'medium_large_size_w', 150 );
    // update_option( 'large_size_h', 150 );
    // update_option( 'large_size_w', 150 );

}
    
$images_project_sizes = array(
    'st'    => array( 'width'=>150, 'height'=>150, 'crop'=>true ), // affecte pc_get_default_st()
    'share' => array( 'width'=>300, 'height'=>300, 'crop'=>true ),
    'gl-m'  => array( 'width'=>800, 'height'=>800, 'crop'=>false ),
    'gl-l'  => array( 'width'=>1200, 'height'=>1200, 'crop'=>false )
);

$images_project_sizes = apply_filters( 'pc_filter_add_img_sizes', $images_project_sizes );

foreach ( $images_project_sizes as $size => $datas ) {
    add_image_size( $size, $datas['width'], $datas['height'], $datas['crop'] );
}


/*=====  FIN Tailles  =====*/

/*===============================
=            Galerie            =
===============================*/
 
add_filter( 'post_gallery', 'pc_gallery_custom', 10, 3 );

function pc_gallery_custom( $output = '', $atts, $instance ) {

    // liste des images
    $img_id_list = explode( ',' , $atts['ids'] );

    // html contruction
    $return = '<ul class="wp-gallery reset-list">';

        foreach ( $img_id_list as $img_id ) {

            $thumbnail_datas = wp_get_attachment_image_src($img_id,'st');

            // si la vignette existe
            if ( isset($thumbnail_datas) && $thumbnail_datas[3] == 1 ) {

               $medium_datas = wp_get_attachment_image_url($img_id,'gl-m');
               if ( !isset($medium_datas) ) { $medium_datas = wp_get_attachment_image_src($value,'full'); }
               $large_datas = wp_get_attachment_image_url($img_id,'gl-l');
               if ( !isset($large_datas) ) { $large_datas = wp_get_attachment_image_src($value,'full'); }

                $caption = wp_get_attachment_caption($img_id);
                $alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true);

                // affichage
                $return .= '<li class="wp-gallery-item">';
                $return .= '<a class="wp-gallery-link" href="'.$large_datas.'" data-gl-caption="'.$caption.'" data-gl-responsive="'.$medium_datas.'" title="Afficher l\'image">';
                $return .= '<img class="wp-gallery-img" src="'.$thumbnail_datas[0].'" width="'.$thumbnail_datas[1].'" height="'.$thumbnail_datas[2].'" alt="'.$alt.'"/>';
                $return .= '</a>';
                $return .= '</li>';

            }

        }

    $return .= '</ul>';

    // affichage
    return $return;

} 
 
 
/*=====  FIN Galerie  =====*/

/*==========================================
=            Image avec légende            =
==========================================*/

add_filter( 'img_caption_shortcode', function( $empty, $attr, $content ) {

    return '<figure class="wp-caption '.$attr['align'].'">'.$content.'<figcaption style="max-width:'.$attr['width'].'px">'.$attr['caption'].'</figcaption></figure>';

}, 10, 3 );


/*=====  FIN Image avec légende  =====*/