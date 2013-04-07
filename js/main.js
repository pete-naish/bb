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
});