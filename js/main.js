var map;

function initialize(bn, al, t, c, pc, inttel, tel) {

	google.maps.visualRefresh = true;

	var mapOptions = {
		center: new google.maps.LatLng(51.866594, -0.185008),
		zoom: 15,
		minZoom: 8,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	map = new google.maps.Map(document.getElementById("find"),
	mapOptions);

	var marker = new google.maps.Marker({
		position: mapOptions.center,
		map: map,
		title:"Beauty Box Knebworth"
	});

	var contentString = '<div class="mapContent">'+
	'<p>'+ bn +'<br/>'+ al +'<br/>' + t +'<br/>' + c +'<br/>' + pc +'</p>'+
	"<p><a href='tel:+'" + inttel + "'>" + tel + "</a></p>"+
	'</div>';

	var infowindow = new google.maps.InfoWindow({
		content: contentString
	});

	google.maps.event.addListener(marker, 'click', function() {
		infowindow.open(map,marker);
	});
};

$(function(){

	if($('.heroSlide').length > 1){
		$('div.hero').bxSlider({
			mode: 'fade',
			pager: false,
			controls: false,
			auto: true,
			autoHover: true
		});
	};
	

	$('html').removeClass('no-js');

	$('nav a, .logo').on('click', function(e) {
		var $this = $(this),
		$hash = $this.attr('href'); // grab the href of the clicked link
		e.preventDefault();

		$('html,body').stop().animate({
			scrollTop: $(this.hash).offset().top - 132
		}, 500); // smooth scroll to the top of the linked section
	});

	if ($(window).width() > 590) {
		$('.treatments').on('click', '.js-load', function(e) {
			var $this = $(this),
				$target = $this.data('link');

			$('.treatmentDisplay').load("/treatments/" + $target + ".php .treatmentDetails", function(response, status, xhr) {
				if (status == "error") {
					var msg = "Sorry, but there was an error: ";
					$('.treatmentDisplay').html("<p class='error'>" + msg + xhr.status + " " + xhr.statusText + "</p>");
				}
			});
			e.preventDefault();
			$this.parent().removeClass('translucent').siblings('div.treatmentIntro').addClass('translucent');
		});

	}

	$('ul.tabs').each(function(){
		// For each set of tabs, we want to keep track of
		// which tab is active and its associated content
		var $active, $content, $links = $(this).find('a');

		// If the location.hash matches one of the links, use that as the active tab.
		// If no match is found, use the first link as the initial active tab.
		// $active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
		$active = $($links.filter('[href="'+location.hash+'"]')[0] || $links.filter('[href="#find"]'));
		$active.addClass('active');
		$content = $($active.attr('href'));

		// Hide the remaining content
		$links.not($active).each(function () {
			$($(this).attr('href')).hide();
		});

		// Bind the click event handler
		$(this).on('click', 'a', function(e){
			// Make the old tab inactive.
			$active.removeClass('active');
			$content.hide();

			// Update the variables with the new link and content
			$active = $(this);
			$content = $($(this).attr('href'));

			// Make the tab active.
			$active.addClass('active');
			$content.show();

			// Prevent the anchor's default click action
			e.preventDefault();

			if ($(this).attr('href') === '#find') {
				// console.log(map);
				google.maps.event.trigger( map, 'resize' );
				map.setCenter(new google.maps.LatLng(51.866594, -0.185008))
			}
		});
	});


});