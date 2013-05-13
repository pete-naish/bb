$(function(){
	$('div.hero').carouFredSel({
		responsive: true,
		width: '100%',
		scroll: {
			fx: "crossfade",
			pauseOnHover: true
		},
		items: {
			height: '50%'
		},
		auto: {
			timeoutDuration: 4000,
			play: false
		}
	});

	function initialize() {
		var mapOptions = {
			center: new google.maps.LatLng(51.866594, -0.185008),
			zoom: 15,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		var map = new google.maps.Map(document.getElementById("map-canvas"),
		mapOptions);

		var marker = new google.maps.Marker({
			position: mapOptions.center,
			map: map,
			title:"Beauty Box Knebworth"
		});

		var contentString = '<div class="mapContent">'+
		'<p>Beauty Box<br/>10 Station Road<br/>Knebworth<br/>Hertfordshire<br/>SG3 6AP</p>'+
		"<p><a href='tel:+441438812804'>01438 812 804</a></p>"+
		'</div>';

		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});

		google.maps.event.addListener(marker, 'click', function() {
			infowindow.open(map,marker);
		});
	}

	google.maps.event.addDomListener(window, 'load', initialize);

	$('nav a, .logo').on('click', function(e) {
		var $this = $(this),
		$hash = $this.attr('href'); // grab the href of the clicked link
		e.preventDefault();

		$('html,body').stop().animate({
			scrollTop: $(this.hash).offset().top - 132
		}, 500); // smooth scroll to the top of the linked section
	});

	$('div.treatmentIntro').hover(
		function() {
			var $this = $(this);
			$this.siblings('div.treatmentIntro').css('opacity', 0.8);
		},
		function() {
			$('div.treatmentIntro').css('opacity', 1);
		});
});