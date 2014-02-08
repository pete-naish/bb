<?php include($_SERVER['DOCUMENT_ROOT'].'/cms/runtime.php'); ?>
<!DOCTYPE html>
<html>
	<head>
		<title>403 Forbidden &#124; Beauty Box Knebworth</title>
		<?php include($_SERVER['DOCUMENT_ROOT'].'/inc/head.php') ?>
	</head>
	<body>
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
		<section id="error">
			<div class="container">
				<div class="error-page">
					<i></i>
					<h3>403 Forbidden - You can't access this</h3>
					<h4 class="mobileHide">Why not try the menu above?</h4>
					<h4 class="desktopHide"><a href="/">Go to the home page</a></h4>

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