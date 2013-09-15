<?php include('cms/runtime.php');?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<title>Beauty Box Knebworth</title>
		<?php perch_content('Site Description and Keywords'); ?>

		<?php include('inc/head.php') ?>
		
	</head>
	<body>
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
		<section id="top" class="mobileHide">
			<div class="container">
				<?php perch_content('Carousel content'); ?>
			</div>
		</section>
		<section id="treatments">
			<div class="container">
				<div class="treatments">
					<i></i>
					<h3><?php perch_content('Treatments heading'); ?></h3>
					<?php perch_content('Treatment intros'); ?>
					<div class="treatmentDisplay"></div>
				</div>
			</div>
		</section>
		<section id="about">
			<div class="container">
				<div class="about">
					<i></i>
					<h3><?php perch_content('About us heading'); ?></h3>
					<div class="row">
						<div class="colThird">
							<h4><?php perch_content('Address heading'); ?></h4>
							<p><?php perch_content('Business name'); ?><br/>
								<?php perch_content('Address line 1'); ?><br/>
								<?php perch_content('Town'); ?><br/>
								<?php perch_content('County'); ?><br/>
								<?php perch_content('Post code'); ?><br/>
								<a href="tel:+<?php perch_content('Phone number international'); ?>" class="tel"><?php perch_content('Phone number'); ?></a>
							</p>
						</div>
						<div class="colThird double">
								<?php perch_content('Reservations section'); ?>
								<?php perch_content('Cancellation policy section'); ?>
							</div>
						</div>
						<div class="colThird">
							<?php perch_content('Opening hours'); ?>
						</div>
					</div>
					<i class="small"></i>
					<div class="row">
						<div class="colThird">
							<?php perch_content('Gift voucher section'); ?>
						</div>
						<div class="colThird">
							<?php perch_content('Childrens section'); ?>
						</div>
						<div class="colThird">
							<?php perch_content('Mens section'); ?>
						</div>
					</div>
				</div>
			</div>
		</section>
		<section id="contact">
			<div class="container">
				<div class="contact">
					<i></i>
					<h3>Contact us</h3>
					<ul class="floatyLinks cf">
						<li class="icon-call"><a href="#">Call us</a></li>
						<li class="icon-email"><a href="#">Email us</a></li>
						<li class="icon-find"><a href="#">Find us</a></li>
						<li class="icon-follow mobileHide"><a href="#">Follow us</a></li>
						<li class="icon-like mobileHide"><a href="#">Like us</a></li>
					</ul>
				</div>
				<div id="map-canvas"></div>
			</div>
		</section>
		<section>
			<div class="container">
				<div class="footer-top mobileHide">
					<i class="small"></i>
					<h5>Available brands</h5>
					<ul class="brand-links">
						<li><a href="#"><img src="img/logo-gelish.png" alt=""></a></li>
						<li><a href="#"><img src="img/logo-caci.png" alt=""></a></li>
						<li><a href="#"><img src="img/logo-hd-brows.png" alt=""></a></li>
						<li><a href="#"><img src="img/logo-minx.png" alt=""></a></li>
						<li><a href="#"><img src="img/logo-sienna-x.png" alt=""></a></li>
					</ul>
				</div>
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