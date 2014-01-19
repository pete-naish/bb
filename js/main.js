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
		var $this = $(this);
		// $hash = $this.attr('href'); // grab the href of the clicked link
		e.preventDefault();

		$('html,body').stop().animate({
			scrollTop: $(this.hash).offset().top - 132
		}, 500); // smooth scroll to the top of the linked section
	});

	
	$('.treatments').on('click', '.js-load', function(e) {
		if ($(window).width() > 590) {
			var $this = $(this),
				$target = $this.data('link');

			$('.treatmentDisplay').load("/treatments/" + $target + ".php .treatmentDetails", function(response, status, xhr) {
				if (status == "error") {
					var msg = "Sorry, but there was an error: ";
					$('.treatmentDisplay').html("<p class='error'>" + msg + xhr.status + " " + xhr.statusText + "</p>");
				}
				setTimeout(function() {
					$('html,body').stop().animate({
						scrollTop: $('.treatmentDisplay').offset().top - 132
					}, 500);
				}, 500);
			});
			e.preventDefault();
			$this.parent().removeClass('translucent').siblings('div.treatmentIntro').addClass('translucent');
			
		}
	});

	$('ul.tabs').each(function(){
		var $active, $content, $links = $(this).find('a');

		$active = $($links.filter('[href="'+location.hash+'"]')[0] || $links.filter('[href="#find"]'));
		$active.addClass('active');
		$content = $($active.attr('href'));

		$links.not($active).each(function () {
			$($(this).attr('href')).hide();
		});

		$(this).on('click', 'a', function(e){
			$active.removeClass('active');
			$content.hide();

			$active = $(this);
			$content = $($(this).attr('href'));

			$active.addClass('active');
			$content.show();

			e.preventDefault();

			if ($(this).attr('href') === '#find') {
				google.maps.event.trigger( map, 'resize' );
				map.setCenter(new google.maps.LatLng(51.866594, -0.185008))
			}
		});
	});


});