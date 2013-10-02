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
				<a href="/" class="logo" title="Beauty Box Knebworth home page"></a>
				<span>
					<h5>For appointments call</h5>
					<a href="tel:+<?php perch_content('Phone number international'); ?>" class="tel"><?php perch_content('Phone number'); ?></a>
				</span>
				<nav class="mobileHide">
					<ul>
						<li><a href="/#treatments">Treatments</a><i></i></li>
						<li><a href="/#about">About us</a><i></i></li>
						<li><a href="/#contact">Contact us</a></li>
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
						<?php perch_content('Treatments and prices'); ?>
					</div>
					<a class="back" href="/#treatments">&lt; Back to treatments</a>
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

	</body>
</html>