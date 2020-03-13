/*=============================
=            Tools            =
=============================*/

/*----------  Conversion em  ----------*/

var rem = function(size,base) {
	if ( base == undefined ) { base = 16; }
	return size / base + 'rem';
};


/*=====  FIN Tools  ======*/

$(document).ready(function(){

/*=================================
=            variables            =
=================================*/

var $win 			= $(window),
	$html 			= $('html'),
	$body 			= $('body'),
	$head 			= $('head'),
	$header 		= $('.header'),
	$main 			= $('.main'),
	$main_header 	= $main.find('.main-header'),
	$primaryNav 	= $('#primary-nav'),

	urlIcons 		= '/wp-content/themes/preform/images/icons/',
	
	win_h, win_w, header_h, win_w_old = 0;

/*=====  End of variables  ======*/

/*==================================
=            Responsive            =
==================================*/

// fonction executée au chargement de la page et à chaque modification de taille de la fenêtre
function winChange() {

	// 768px
	if (window.matchMedia('(min-width: 48em)').matches) {

	} else {

	}

	/*----------  Fullscreen  ----------*/
	
	if ( $html.hasClass('is-fullscreen') ) {

		win_h = $win.height() + 100;
		win_w = $win.width();
		header_h = $header.outerHeight();		

		if ( win_w != win_w_old ) {
			// éléments concernés par le plein écran
			$main_header.css( 'min-height', rem(win_h) );
			$main.css( 'margin-top', rem(win_h-header_h) );
			win_w_old = win_w;
		}

	}
	


} // fin winWidthChange()

winChange();

$win.resize(winChange);


/*=====  End of Responsive  ======*/

/*==================================
=            navigation            =
==================================*/

/*----------  btn open/close  ----------*/

$('.js-h-nav').click(function() {
	$html.toggleClass('h-nav-is-open');
});

/*----------  Thème fullscreen  ----------*/

if ( $html.hasClass('theme-fullscreen')) {

	$('.fs-more-btn').click(function() {
		$('html, body').animate({ scrollTop:win_h }, 500);
	});

}


/*=====  End of navigation  ======*/

/*==============================
=            divers            =
==============================*/

/*----------  Supprimme le focus après un clic  ----------*/

$('button:not(.overlay)').mouseup(function() {
	$(this).blur();	
});


/*----------  gallery  ----------*/

$('.wp-gallery').gallery({
	btnNextInner:sprite.arrow,
	btnPrevInner:sprite.arrow,
	btnCloseInner:sprite.cross,
	moveDuration:500,
	responsiveImg:true
});


/*----------  iframe  ----------*/

$('.editor iframe').each(function() {
	$(this).wrap('<div class="iframe iframe_16-10"></div>');
});


/*=====  End of divers  ======*/

/*=============================
=            Popup            =
=============================*/

var $popBtnOpen = $('.btn-display-pop');

if ( $popBtnOpen.length > 0 ) {

	var $pop = $('.pop'), 
	jsMap = false, // Leaflet chargé ?
	mapLat,
	mapLong, 
	mapZoomDefault = 15,
	mapZoomCurrent = mapZoomDefault,
	mapZoomMin = 12,
	mapZoomMax = 18;
	
	var pc_pop_close = function() {
		$pop.removeClass('is-visible').empty();
		$body.off('keydown', pc_pop_escap);
	};

	var pc_pop_escap = function() {
		if ( event.keyCode == 27 ) { // échap
			pc_pop_close();
		}
	};

	$popBtnOpen.click(function() {

		$this = $(this);

		if ( $this.data('cible') == 'map' ) {

			// ajout  container map et contrôles
			$pop.addClass('pop--map').append('<div id="map" class="pop-inner map"></div><div class="pop-controls"><button type="button" class="pop-btn-hide btn reset-btn" title="Fermer" data-cible="map">'+sprite.cross+'</button><button type="button" class="map-btn-zoom map-btn-zoom--in btn reset-btn">zoom +</button><button type="button" class="map-btn-zoom map-btn-zoom--out btn reset-btn">zoom -</button></div>');
			// coordonnées GPS
			var mapLat = $this.data('lat'),
			mapLong = $this.data('long'),
			// boutons du zoom
			$mapBtnZoom = $pop.find('.map-btn-zoom').mouseup(function() { $(this).blur();}),
			$mapBtnZoomIn = $pop.find('.map-btn-zoom--in'),
			$mapBtnZoomOut = $pop.find('.map-btn-zoom--out');
			// actions pour fermer la pop
			$pop.find('.pop-btn-hide').on('click', pc_pop_close);
			$body.on('keydown', pc_pop_escap);

			// chargement Leaflet si pas déjà fait
			if ( !jsMap ) {

				jsMap = true;

				// css
				var $contactMapCssTag = $(document.createElement('link'));

				$contactMapCssTag.attr('href', 'https://unpkg.com/leaflet@1.6.0/dist/leaflet.css');
				$contactMapCssTag.attr('rel', 'stylesheet');
				$contactMapCssTag.attr('integrity', 'sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==');
				$contactMapCssTag.attr('crossorigin', '');

				$head.append($contactMapCssTag);

				// js
				var $contactMapJsTag = $(document.createElement('script'));

				$contactMapJsTag.attr('src', 'https://unpkg.com/leaflet@1.6.0/dist/leaflet.js');
				$contactMapJsTag.attr('integrity', 'sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==');
				$contactMapJsTag.attr('crossorigin', '');

				$body.append($contactMapJsTag);

			}


			var map_add = function() {
				
				// fonction appelée toutes les 100 ms tant que la méthode Leaflet n'est pas prête
				if ( typeof L === 'undefined' ) { return; } else {

					// stop l'appel toutes 100 ms
					clearInterval(waitForMapReady);

					$pop.addClass('is-visible');
					$body.on('keydown', pc_pop_escap);

					// création de l'object
					var map = L.map('map', {
						minZoom : mapZoomMin,
						maxZoom : mapZoomMax,
						zoomControl : false,
						scrollWheelZoom : false,
						tap : false
					}).setView([mapLat, mapLong], mapZoomDefault);
					L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoicGFwaWVyY29kZSIsImEiOiJjaWoxem42NHUwMDZidjBtNWVienk2YnRwIn0.6CFJqIA2nRlML7EWwZBisA', {
						id: 'mapbox/streets-v11',
						tileSize: 512,
						zoomOffset: -1
					}).addTo(map);

					// marqueur
					var mapIcon = L.divIcon({
						iconSize: [40,60],
						iconAnchor: [20,60],
						className: 'map-marker'
						});

					// affichage de la map
					var marker = L.marker([mapLat, mapLong], {icon: mapIcon}).addTo(map);

					// favicon dans marqueur
					$('.map-marker').css({
						'background-image':'url('+$head.find('link[rel=icon]').attr('href')+')',
						'background-position':'5px 5px',
						'background-repeat':'no-repeat',
						'background-size':'30px 30px'
					});
			
					// zoom +
					$mapBtnZoomIn.click(function() {
						map.zoomIn();
						mapZoomCurrent++;
						if ( mapZoomCurrent == mapZoomMax ) { $(this).prop('disabled',true); }
						if ( mapZoomCurrent == mapZoomMin + 1 ) { $mapBtnZoomOut.prop('disabled',false); }
						
					});

					// zoom -
					$mapBtnZoomOut.click(function() {
						map.zoomOut();
						mapZoomCurrent--;
						if ( mapZoomCurrent == mapZoomMin ) { $(this).prop('disabled',true); }
						if ( mapZoomCurrent == mapZoomMax - 1 ) { $mapBtnZoomIn.prop('disabled',false); }
					});
		
				}
			};

			// toutes les 100 ms tant que la méthode Leaflet n'est pas prête
			var waitForMapReady = setInterval( map_add, 100 );

		}


	}); // FIN $popBtnOpen.click

} // FIN if $popBtnOpen


/*=====  FIN Popup  =====*/

/*=======================================
=            Message cookies            =
=======================================*/

// cookie es-tu là ?
/*
if (getCookie('cookies') === '') {

	// création du message
	$('body').prepend('<p class="cookies-msg is-hidden no-print">En poursuivant votre navigation sur ce site, vous acceptez l’utilisation de <strong>cookies</strong>, <a href="'+cguUrl+'" title="Conditions générales d\'utilisation" class="cookies-msg-link" rel="nofollow">en savoir plus</a>. <button type="button" class="btn cookies-msg-btn reset-btn">J\'accepte</button></p>');

	var $cookiesMsg = $('.cookies-msg');

	// apparition du message
	$cookiesMsg.removeClass('is-hidden');

	// btn de validation des cookies
	$('.btn-alert-cookie').click(function() {

		// création du cookie, valable un an
		setCookie('cookies', 'accepted', 365);
		// disparition du message
		$cookiesMsg.addClass('is-hidden');
		// suppression du message
		setTimeout(function(){ $cookiesMsg.remove(); }, 500); // durée à reporter en css

	});

}
*/


/*=====  FIN Message cookies  ======*/

}); // end $(document).ready()
