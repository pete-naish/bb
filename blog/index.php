<?php
    include($_SERVER['DOCUMENT_ROOT'].'/cms/runtime.php');
    include('inc/contact-app/headers.php');
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <title><?php perch_content('Business name'); ?> <?php perch_content('Town'); ?></title>
        <?php perch_content('Site Description'); ?>
        <?php include($_SERVER['DOCUMENT_ROOT'].'/inc/head.php') ?>
    </head>
    <body>
        <header>
            <div class="container">
                <h1 class="visuallyhidden"><?php perch_content('Business name'); ?> <?php perch_content('Town'); ?></h1>
                <a href="/" class="logo" title="Beauty Box Knebworth home page"></a>
                <span>
                    <h5>For appointments call</h5>
                    <a href="tel:+<?php perch_content('Phone number international'); ?>" class="tel"><?php perch_content('Phone number'); ?></a>
                </span>
                <nav class="mobileHide">
                    <ul>
                        <li><a href="/#treatments">Treatments</a><i></i></li>
                        <li><a href="/#about">About us</a><i></i></li>
                        <li><a href="/blog">The blog</a><i></i></li>
                        <li><a href="/#contact" class="external">Contact us</a></li>
                        <!-- <li><a href="http://www.lovebeautybox.co.uk/" class="external">Shop online</a></li> -->
                    </ul>
                </nav>
            </div>
        </header>
        <section id="blog">
            <div class="container">
                <div class="treatments">
                    <i></i>
                    <h3>The blog</h3>
                    <?php 
                        perch_blog_recent_posts(10);
                    ?>
                    
                    <p><a href="archive.php">More posts</a></p>
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
        <!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDxCUv6c3GVhjrwHgqzAzJ7sBO7gIr0ltw&amp;sensor=true"></script>
        <script>
            window.jQuery || document.write('<script src="/js/jquery-1.9.1.min.js"><\/script>');
        </script>
        <script src="/js/plugins.min.js"></script>
        <script src="/js/main.min.js"></script>
        <script src="/js/json.js"></script>
        <script src='inc/contact-app/js/init.php'></script>
        <script>
            google.maps.event.addDomListener(window, 'load', initialize("<?php perch_content('Business name'); ?>", "<?php perch_content('Address line 1'); ?>", "<?php perch_content('Town'); ?>", "<?php perch_content('County'); ?>", "<?php perch_content('Post code'); ?>", "<?php perch_content('Phone number international'); ?>", "<?php perch_content('Phone number'); ?>"));
        </script>-->
    </body>
</html>