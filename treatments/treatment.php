<?php include($_SERVER['DOCUMENT_ROOT'].'/cms/runtime.php'); ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<title><?php perch_pages_title(); ?></title>
		<?php perch_content('Treatment Page Description and Keywords'); ?>

		<?php include($_SERVER['DOCUMENT_ROOT'].'/inc/head.php') ?>
		
	</head>
	<body class="treatment-page">
		<header>
			<div class="container">
				<h1 class="visuallyhidden">Beauty Box Knebworth</h1>
				<a href="#top" class="logo" title="Beauty Box Knebworth home page"></a>
				<span>
					<h5>For appointments call</h5>
					<a href="tel:+<?php perch_content('Phone number international'); ?>" class="tel"><?php perch_content('Phone number'); ?></a>
				</span>
				<nav class="mobileHide">
					<ul>
						<li><a href="#treatments">Treatments</a><i></i></li>
						<li><a href="#about">About us</a><i></i></li>
						<li><a href="#contact">Contact us</a></li>
					</ul>
				</nav>
			</div>
		</header>
		
		<section id="treatments">
			<div class="container">
				<div class="treatments">
					<div class="treatmentDetails cf">
						<i></i>
						<h3><?php perch_content('Treatment category'); ?></h3>
						<h4>Treatments &amp; prices</h4>
						<div class="colHalf">
							<?php perch_content('Treatment'); ?>
						</div>
						<div class="colHalf">
							<div class="border">
								<div class="treatment">
									<h4>NSI Acrylic Nail Extensions</h4>
									<p class="notes">Nail art and Diamantes available, with price on request.</p>
								</div>
								<div class="treatment">
									<h4>Gelish</h4>
									<p>Gelish can provide up to 3 weeks worth of wear. There's no drying time, no smudges, and it soaks off in 20 minutes - with no damage to the natural nail!</p>
									<p class="notes">Gelish removal Free or &pound;10 without new set being re-applied.</p>
								</div>
								<div class="treatment">
									<h4>Minx nails</h4>
									<p>Extending fashion to the fingers and toes, Minx Nails is the hottest way to decorate your nails.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		
		<section>
			<div class="container">
				<div class="footer-bottom">
					<h5><?php perch_content('Business name'); ?>, <?php perch_content('Address line 1'); ?>, <?php perch_content('Town'); ?>, <?php perch_content('County'); ?>, <?php perch_content('Post code'); ?></h5>
				</div>
			</div>
		</section>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDxCUv6c3GVhjrwHgqzAzJ7sBO7gIr0ltw&amp;sensor=true"></script>
		<script>
			if (window.innerWidth > 590) {

				window.jQuery || document.write('<script src="js/jquery-1.9.1.min.js"><\/script>');

				document.write('<script src="js/plugins.min.js"><\/script>');

				document.write('<script src="js/main.js"><\/script>');
			}

			function initialize(bn, al, t, c, pc, inttel, tel) {
				// use visual refresh!
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

			google.maps.event.addDomListener(window, 'load', initialize("<?php perch_content('Business name'); ?>", "<?php perch_content('Address line 1'); ?>", "<?php perch_content('Town'); ?>", "<?php perch_content('County'); ?>", "<?php perch_content('Post code'); ?>", "<?php perch_content('Phone number international'); ?>", "<?php perch_content('Phone number'); ?>"));
			
		</script>

	</body>
</html>