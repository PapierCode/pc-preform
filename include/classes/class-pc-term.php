<?php

class PC_Term {

	public $wp_term;	// object

	public $taxonomy;	// string
	public $name;		// string
	public $id;			// int
	public $slug;		// string
	public $parent;		// int
	public $childrens;	// array
	public $count;		// int

	public $permalink;	// string
	public $metas;		// array

	public $has_image;	// bool
	

	/*=================================
	=            Construct            =
	=================================*/
	
	public function __construct ( WP_Term $wp_term ) {

		// WP_term
		$this->wp_term 		= $wp_term;
		$this->taxonomy 	= $wp_term->taxonomy;
		$this->name 		= $wp_term->name;
		$this->id 			= $wp_term->term_id;
		$this->slug 		= $wp_term->slug;
		$this->parent 		= $wp_term->parent;
		$this->count 		= $wp_term->count;

		// enfants
		$this->childrens	= get_term_children( $wp_term->term_id, $wp_term->taxonomy );
		// url
		$this->permalink 	= get_term_link( $wp_term->term_id );

		// metas
		$this->metas 		= get_term_meta( $wp_term->term_id );
		// simplifcation tableau métas
		foreach ( $this->metas as $key => $value) {
			$this->metas[$key] = implode('', $this->metas[$key] );
		}

		// test image associée
		$this->has_image = ( isset( $this->metas['visual-id'] ) && is_object( get_post( $this->metas['visual-id'] ) ) ) ? true : false;

	}
 
	
	/*=====  FIN Construct  =====*/

	/*==============================
	=            Résumé            =
	==============================*/
	
	/**
	 * 
	 * [RESUMÉ] Titre
	 * 
	 * @return string Méta resum-title | titre du post | '(sans titre)'
	 * 
	 */
	public function get_card_title() {

		$metas = $this->metas;
		$title = ( isset( $metas['resum-title'] ) ) ? $metas['resum-title'] : $this->name;

		global $texts_lengths;
		return pc_words_limit( $title, $texts_lengths['resum-title'] );

	}

	/**
	 * 
	 * [RESUMÉ] Description
	 * 
	 * @return string Méta resum-desc | wp_excerpt | empty
	 * 
	 */
	public function get_card_description() {

		$metas = $this->metas;

		if ( isset( $metas['resum-desc'] ) ) {

			$description = $metas['resum-desc'];
			
		} else if ( isset( $metas['content-desc'] ) ) {

			$description = wp_strip_all_tags( $metas['content-desc'] );

		} else {

			$description = '';

		}
	
		global $texts_lengths;
		return pc_words_limit( $description, $texts_lengths['resum-desc'] );
	
	}

	/**
	 * 
	 * [RESUMÉ] Urls & attribut alt de la vignette
	 * 
	 * @return 	array 	array 	urls st-400/st-500/st-700	Méta visual-id | $card_image_default_datas
	 * 					string	attribut alt				Méta _wp_attachment_image_alt | get_card_title()
	 * 
	 */
	public function get_card_image_datas() {

		$metas = $this->metas;

		if ( $this->has_image ) {
			
			$thumbnail_datas['urls'] = array(
				wp_get_attachment_image_src( $metas['visual-id'], 'st-400' )[0],
				wp_get_attachment_image_src( $metas['visual-id'], 'st-500' )[0],
				wp_get_attachment_image_src( $metas['visual-id'], 'st-700' )[0]
			);
			
			$image_alt = get_post_meta( $metas['visual-id'], '_wp_attachment_image_alt', true );
			$thumbnail_datas['alt'] = ( '' != $image_alt ) ? $image_alt : $this->get_card_title();
		
		} else {

			$thumbnail_datas['urls'] = pc_get_default_card_image();
			$thumbnail_datas['alt'] = $this->get_card_title();

		}
		
		return $thumbnail_datas;
	
	}
	
	/**
	 * 
	 * [RESUMÉ] Affichage vignette
	 * 
	 * @return string HTML
	 * 
	 */
	public function display_card_image() {

		global $images_sizes;
		$thumbnail_datas = $this->get_card_image_datas();
	
		$attr_srcset = $thumbnail_datas['urls'][0].' 400w, '.$thumbnail_datas['urls'][1].' 500w, '.$thumbnail_datas['urls'][2].' 700w';
		$attr_sizes = '(max-width:400px) 400px, (min-width:401px) and (max-width:759px) 700px, (min-width:760px) 500px';
	
		$tag = '<img src="'.$thumbnail_datas['urls'][1].'" alt="'.$thumbnail_datas['alt'].'" srcset="'.$attr_srcset.'" sizes="'.$attr_sizes.'" loading="lazy" width="'.$images_sizes['st-500']['width'].'" height="'.$images_sizes['st-500']['height'].'" />';
		
		echo $tag;
	
	}
	
	/**
	 * 
	 * [RESUMÉ] Affichage résumé complet
	 * 
	 * @param	int		$title_level	Niveau du titre
	 * @param	string	$classes_css	Classes css
	 * 
	 * @return	string	HTML
	 * 
	 */

	public function display_card( $title_level = 2, $classes_css = 'st-inner' ) {

		$metas = $this->metas;
	
		// titre
		$title = $this->get_card_title();
		// lien
		$link_tag_start = '<a href="'.$this->permalink.'" class="st-link" title="Lire la suite de : '.$title.'">';
		$link_position = apply_filters( 'pc_filter_post_card_link_position', 'title', $this ); // title || st-inner
		// description
		$description = $this->get_card_description();

		// filtres call to action
		$ico_more = apply_filters( 'pc_filter_card_ico_more', pc_svg('arrow'), $this );	
		$ico_more_display = apply_filters( 'pc_filter_card_ico_more_display', true, $this );
		$read_more_display = apply_filters( 'pc_filter_card_read_more_display', false, $this );
		
		
		/*----------  Affichage  ----------*/
		
		echo '<article class="'.$classes_css.'">';
	
			if ( 'st-inner' == $link_position ) { echo $link_tag_start; }
	
				// hook
				do_action( 'pc_term_card_after_start', $this );
			
				echo '<figure class="st-figure">';
					$this->display_card_image();				
				echo '</figure>';

				// hook	
				do_action( 'pc_term_card_after_figure', $this );
	
				echo '<h'.$title_level.' class="st-title">';
					if ( 'title' == $link_position ) {
						echo $link_tag_start.$title.'</a>';
					} else {
						echo $title;
					}
				echo '</h'.$title_level.'>';	
	
				// hook	
				do_action( 'pc_term_card_after_title', $this );
				
				if ( '' != $description ) {
					echo '<p class="st-desc">';
						echo $description;
						if ( $ico_more_display ) { echo ' <span class="st-desc-ico">'.$ico_more.'</span>';	}	
					echo '</p>';
				}

				// hook
				do_action( 'pc_term_card_after_desc', $this );
				
				if ( $read_more_display ) {
					echo '<div class="st-read-more" aria-hidden="true"><span class="st-read-more-ico">'.$ico_more.'</span> <span class="st-read-more-txt">Lire la suite</span></a></div>';
				}
			
				// hook
				do_action( 'pc_term_card_before_end', $this );
	
			if ( 'st-inner' == $link_position ) { echo '</a>'; }
		
		echo '</article>';
		
	}	
	
	
	/*=====  FIN Résumé  =====*/

	/*====================================
	=            SEO & Social            =
	====================================*/

	/**
	 * 
	 * [SEO & SOCIAL] Titre
	 * 
	 * @return string Méta seo-title | get_card_title()
	 * 
	 */
	public function get_seo_meta_title() {

		$metas = $this->metas;
		global $settings_project;
		$title = ( isset( $metas['seo-title'] ) ) ? $metas['seo-title'] : $this->get_card_title().' - '.$settings_project['coord-name'];
		
		return $title;
	
	}

	/**
	 * 
	 * [SEO & SOCIAL] Description
	 * 
	 * @return string Méta seo-desc | get_card_description()
	 * 
	 */
	public function get_seo_meta_description() {

		$metas = $this->metas;
		if ( isset( $metas['seo-desc'] ) ) {
			
			$description = $metas['seo-desc'];
			
		} else if ( '' != $this->get_card_description() ) {

			$description = $this->get_card_description();

		} else {

			global $settings_project;
			$description = $settings_project['seo-desc'];

		}
		
		global $texts_lengths;
		return pc_words_limit( $description, $texts_lengths['seo-desc'] );
	
	}

	/**
	 * 
	 * [SEO & SOCIAL] Url et dimensions de l'image
	 * 
	 * @return array 	méta visual-id | default
	 * 					Url/width/height 
	 * 
	 */
	public function get_seo_meta_image_datas() {

		$metas = $this->metas;
		$image = ( $this->has_image ) ? wp_get_attachment_image_src( $metas['visual-id'], 'share' ) : pc_get_default_image_to_share();
		
		return $image;
	
	}

	/**
	 * 
	 * [SEO & SOCIAL] Titre, Description, Image
	 * 
	 * @param 	array	$seo_metas	Valeurs par défaut
	 * 
	 * @return	array	get_seo_meta_title()
	 * 					get_seo_meta_description()
	 * 					get_seo_meta_image_datas()
	 * 
	 */
	public function get_seo_metas( ) {

		// titre
		$metas['title'] = $this->get_seo_meta_title();
		// description
		$metas['description'] = $this->get_seo_meta_description();
		// image
		$metas['image'] = $this->get_seo_meta_image_datas();
		// url
		$metas['permalink'] = $this->permalink;

		return $metas;

	}
	
	
	/*=====  FIN SEO & Social  =====*/


}
