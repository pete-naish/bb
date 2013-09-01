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

	

	

	

	$('nav a, .logo').on('click', function(e) {
		var $this = $(this),
		$hash = $this.attr('href'); // grab the href of the clicked link
		e.preventDefault();

		$('html,body').stop().animate({
			scrollTop: $(this.hash).offset().top - 132
		}, 500); // smooth scroll to the top of the linked section
	});

	// $('div.treatmentIntro').hover(
	// 	function() {
	// 		var $this = $(this);
	// 		$this.siblings('div.treatmentIntro').css('opacity', 0.8);
	// 	},
	// 	function() {
	// 		$('div.treatmentIntro').css('opacity', 1);
	// 	});
	
	$('div.treatmentIntro').hover(function() {
			var $this = $(this);
			$this.removeClass('translucent').siblings('div.treatmentIntro').addClass('translucent');
		});
});