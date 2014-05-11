<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Solstice Studios</title>
        <meta name="description" content="Solstice Studios - Design and Development from Chicago, Illinois">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta property="og:image" content="img/logo.png" />
        <link rel="stylesheet" href="css/normalize.min.css">
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        <script src="js/vendor/smooth-scroll.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <?php
			function spamcheck($field) {
			  // Sanitize e-mail address
			  $field=filter_var($field, FILTER_SANITIZE_EMAIL);
			  // Validate e-mail address
			  if(filter_var($field, FILTER_VALIDATE_EMAIL)) {
			    return TRUE;
			  } else {
			    return FALSE;
			  }
			}
		?>

        <div data-scroll-header class="header-container">
            <header class="wrapper clearfix">
                <a data-scroll href="#top"><img src="img/logo.png" alt="Solstice Studios Logo" /></a>
                <nav>
                    <ul>
                        <li><a data-scroll href="#about">About</a></li>
                        <li><a data-scroll href="#services">Services</a></li>
                        <li><a data-scroll href="#contact">Contact</a></li>
                    </ul>
                </nav>
            </header>
        </div>

        <div class="main-container" id="top">
        	<div class="wrapper" id="tagline">
            	<h1>Something something something we're awesome you should work with us</h1>
            </div> <!-- .tagline -->
            <div id="about">
	            <div class="wrapper">
	            	<h2>About</h2>
	            	<p><span>Our Philosophy:</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus fermentum lacus sagittis semper lobortis. Sed velit ipsum, volutpat id nisl ac, venenatis blandit odio. Nulla luctus  est sit amet luctus mollis.</p>
	            	<h2>Who We Are</h2>
	            	<div class="profiles">
	            		<div class="profile">
	            			<img src="img/steve.png" alt="Steven Perry" />
		            		<h3>Steven Perry <br/>
		            		Lead Programmer</h3>
		            		<p>bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio</p>
	            		</div><!--
	            		--><div class="profile">
	            			<img src="img/alex.png" alt="Alex Poling" />
		            		<h3>Alex Poling <br />
		            		Project Manager</h3>
		            		<p>bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio</p>
	            		</div><!--
	            		--><div class="profile">
	            			<img src="img/carolyne.png" alt="Carolyne Rex" />
		            		<h3>Carolyne Rex <br />
		            		Design Specialist</h3>
		            		<p>bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio bio</p>
	            		</div>
	            	</div>
	            </div>
            </div>
            <div class="wrapper" id="services">
            	<h2>Services</h2>
            	<div class="service">
	            	<h3>Brand Consulting</h3>
	            	<p>Lorem ipsum dolor sit amet, con-
	sectetur adipiscing elit. In auctor
	
	massa dapibus dolor tempus, ut euis-
	mod nunc venenatis. Nam nec velit 
	
	tellus. Aliquam elementum pellen-
	tesque elementum. Vestibulum ante 
	
	ipsum primis in faucibus orci luctus et 
	
	ultrices posuere cubilia Curae; In hac 
	
	habitasse platea dictumst. Cras ultri-
	cies dictum ligula nec vulputate.</p>
            	</div>
            	<div class="service">
	            	<h3>Web Design</h3>
	            	<p>Lorem ipsum dolor sit amet, con-
	sectetur adipiscing elit. In auctor
	
	massa dapibus dolor tempus, ut euis-
	mod nunc venenatis. Nam nec velit 
	
	tellus. Aliquam elementum pellen-
	tesque elementum. Vestibulum ante 
	
	ipsum primis in faucibus orci luctus et 
	
	ultrices posuere cubilia Curae; In hac 
	
	habitasse platea dictumst. Cras ultri-
	cies dictum ligula nec vulputate.</p>
            	</div>
            </div>
            <div id="contact">
            	<div class="wrapper">
            		<h2>Contact</h2>
            		<?php
						// display form if user has not clicked submit
						if (!isset($_POST["submit"])) {
						  ?>
						  <form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
						  <label><span>From:</span> <input type="text" name="from" cols="40" placeholder="john.doe@gmail.com"></label>
						  <label><span>Subject:</span> <input type="text" name="subject" cols="40"></label>
						  <label><span>Message:</span> <textarea rows="10" cols="40" name="message"></textarea></label>
						  <input type="submit" name="submit" value="Send Message">
						  </form>
						  <?php 
						} else {  // the user has submitted the form
						  // Check if the "from" input field is filled out
						  if (isset($_POST["from"])) {
						    // Check if "from" email address is valid
						    $mailcheck = spamcheck($_POST["from"]);
						    if ($mailcheck==FALSE) {
						      echo "<h3>Invalid email formatting</h3>";
						    } else {
						      $from = $_POST["from"]; // sender
						      $subject = $_POST["subject"];
						      $message = $_POST["message"];
						      // message lines should not exceed 70 characters (PHP rule), so wrap it
						      $message = wordwrap($message, 70);
						      // send mail
						      mail("solsticestudios.design@gmail.com",$subject,$message,"From: $from\n");
						      echo "Thank you for messaging us. We'll be in contact with you soon!";
						    }
						  }
						}
					?>
            	</div>
            </div>
        </div> <!-- #main-container -->

        <div class="footer-container">
            <footer class="wrapper">
                <h3>&copy; <?php echo date("Y") ?> Solstice Studios <br/></h3>
            </footer>
        </div>
		<script>
    		smoothScroll.init({ 
    			speed: 750, // Integer. How fast to complete the scroll in milliseconds
    			easing: 'easeInOutCubic', // Easing pattern to use
    			updateURL: false // Boolean. Whether or not to update the URL with the anchor hash on scroll
    		});
		</script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>')</script>
        <script src="js/main.js"></script>
        <script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		  ga('create', 'UA-49384619-1', 'solstice-studios.com');
		  ga('send', 'pageview');
		
		</script>
    </body>
</html>
