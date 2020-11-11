<?php

do_action( 'pc_footer_start' );
	
	do_action( 'pc_footer_before_address' );

	/*===================================
	=            Coordonnées            =
	===================================*/ ?>
	
	<address class="coord">
		<?php global $settings_project; ?>
		<dl class="coord-list">
			<dt class="coord-item coord-item--logo">
				<?php
					$logo_footer_datas = array(
						'url' => get_bloginfo('template_directory').'/images/logo-footer.svg',
						'width' => 100,
						'height' => 25,
						'alt' => 'Logo '.$settings_project['coord-name']
					);
					$logo_footer_datas = apply_filters( 'pc_filter_footer_logo', $logo_footer_datas );
					?>
					<img src="<?= $logo_footer_datas['url']; ?>" alt="<?= $logo_footer_datas['alt']; ?>" width="<?= $logo_footer_datas['width']; ?>" height="<?= $logo_footer_datas['height']; ?>" />
			</dt>
			<dd class="coord-item coord-item--addr">
				<span class="coord-ico"><?= pc_svg('map'); ?></span>
				<span class="coord-txt">
					<?= $settings_project['coord-address'].' <br/>'.$settings_project['coord-postal-code'].' '.$settings_project['coord-city']; ?>

					<?php if ( $settings_project['coord-lat'] != '' && $settings_project['coord-long'] != '' ) { ?>
						<br aria-hidden="true"/><button class="reset-btn btn-display-pop no-print" data-cible="map" data-lat="<?= $settings_project['coord-lat']; ?>" data-long="<?= $settings_project['coord-long']; ?>" aria-hidden="true">Afficher la carte</button>
					<?php } ?>

				</span>
			</dd>
			<dd class="coord-item coord-item--phone">
				<span class="coord-ico"><?= pc_svg('phone'); ?></span>
				<span class="coord-txt">
					<a href="tel:<?= pc_phone($settings_project['coord-phone-1']); ?>"><?= $settings_project['coord-phone-1']; ?></a>
					<?php if ( $settings_project['coord-phone-2'] != '' ) {
						echo '<br/><span class="coord-sep"> - </span><a href="tel:'.pc_phone($settings_project['coord-phone-2']).'">'.$settings_project['coord-phone-2'].'</a>';
					} ?>
				</span>
			</dd>
		</dl>
		<?php pc_display_schema_local_business(); ?>
	</address>

	<?php 
	
	do_action( 'pc_footer_before_address' );
	

	/*=====  FIN Coordonnées  =====*/

	do_action( 'pc_footer_before_nav' );

	/*==================================
	=            Navigation            =
	==================================*/ ?>

	<nav id="footer-nav" class="f-nav">
		<ul class="f-nav-list f-nav-list--l1 f-p-nav-list f-p-nav-list--l1 reset-list">
			<li class="f-nav-item f-nav-item--l1 f-p-nav-item f-p-nav-item--l1">&copy; <?= $settings_project['coord-name']; ?></li>
		<?php
			$nav_footer_config = array(
				'theme_location'  	=> 'nav-footer',
				'nav_prefix'		=> array('f-nav','f-p-nav'), // custom
				'items_wrap'      	=> '%3$s',
				'depth'           	=> 1,
				'item_spacing'		=> 'discard',
				'container'       	=> '',
				'fallback_cb'     	=> false,
				'walker'          	=> new Pc_Walker_Nav_Menu()
			);
			wp_nav_menu( $nav_footer_config );
		?>
		</ul>
	</nav>

	<?php 
	
	do_action( 'pc_footer_after_nav' );
	

	/*=====  FIN Navigation  =====*/
	

do_action( 'pc_footer_end' );

wp_footer(); 
do_action( 'pc_wp_footer' ); ?>

</body>
</html>
