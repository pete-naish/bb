<?php include('cms/runtime.php');?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<title><?php perch_content('Business name'); ?> <?php perch_content('Town'); ?></title>
		<?php perch_content('Site Description and Keywords'); ?>

		<?php include('inc/head.php') ?>
		
	</head>
	<body>
		<header>
			<div class="container">
				<h1 class="visuallyhidden"><?php perch_content('Business name'); ?> <?php perch_content('Town'); ?></h1>
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
					<h3><?php perch_content('Contact heading'); ?></h3>
					<ul class="floatyLinks cf tabs">
						<li class="icon-call"><a href="#call">Call us</a></li>
						<li class="icon-email"><a href="#email">Email us</a></li>
						<li class="icon-find"><a href="#map-canvas">Find us</a></li>
						<li class="icon-follow mobileHide"><a href="#follow">Follow us</a></li>
						<li class="icon-like mobileHide"><a href="#like">Like us</a></li>
					</ul>
				</div>
				<div id="call" class="tab-content"><h3><a href="tel:+<?php perch_content('Phone number international'); ?>" class="tel"><?php perch_content('Phone number'); ?></a></h3></div>
				<div id="email" class="tab-content"><?php perch_form('contact.html'); ?></div>
				<div id="map-canvas"></div>
				<div id="follow" class="tab-content">
					<div class="feed feed-twitter"></div>
				</div>
				<div id="like" class="tab-content">
					<div class="feed feed-facebook">
						<h1><a href="https://www.facebook.com/thebeautyboxknebworth">/TheBeautyBoxKnebworth</a></h1>
						<?php include('fb-feed.php'); ?>
					</div>
				</div>
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
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDxCUv6c3GVhjrwHgqzAzJ7sBO7gIr0ltw&amp;sensor=true"></script>
		<script>
			window.jQuery || document.write('<script src="js/jquery-1.9.1.min.js"><\/script>');
		</script>
		<script src="js/plugins.min.js"></script>
		<script src="js/main.js"></script>
		<script src="js/twitterfeed.js"></script>
		<script>
			google.maps.event.addDomListener(window, 'load', initialize("<?php perch_content('Business name'); ?>", "<?php perch_content('Address line 1'); ?>", "<?php perch_content('Town'); ?>", "<?php perch_content('County'); ?>", "<?php perch_content('Post code'); ?>", "<?php perch_content('Phone number international'); ?>", "<?php perch_content('Phone number'); ?>"));
		</script>
	</body>
</html>