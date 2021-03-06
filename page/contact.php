<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <title>StevenPerry.net</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
        <link href='http://fonts.googleapis.com/css?family=Arvo:400,700|Ubuntu:400,700' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="../assets/css/master.css">

        <script src="../assets/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
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
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand visible-xs" href="../index.html">Steven Perry</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="../index.html">Home</a></li>
            <li><a href="work.html">Work</a></li>
            <li><a href="resume.html">Resume</a></li>
            <li class="active"><a href="contact.php">Contact</a></li>
          </ul>
          <a class="navbar-brand pull-right hidden-xs" href="../index.html">Steven Perry</a>
        </div><!--/.navbar-collapse -->
      </div>
    </div>

    <div class="container contact">
		<div class="text-center"><h1>Contact</h1></div>
		<hr class="title">
  		<div class="row">
  			<div class="col-md-6 text-center">
				<h2>Have an idea?</h2>
				<p>I'm always open to new projects.</p>
				<p> Please feel free to contact me via <a href="mailto:perryst5@gmail.com">e-mail</a>.</p>
				<h2>Address</h2>
				<p>2791 Northwind Drive</p>
				<p>Apartment 59</p>
				<p>East Lansing, MI</p>
				<p>48823</p>
				<h2>Phone</h2>
				<p>(586)-850-0628</p>
			</div>
			<div class="col-md-6">
				<?php
					// display form if user has not clicked submit
					if (!isset($_POST["submit"])) {
					  ?>
					  <h2>Message Me!</h2>
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
					      mail("perryst5@gmail.com",$subject,$message,"From: $from\n");
					      echo "<p>Thank you for messaging me. I'll be in contact with you soon!</p>";
					    }
					  }
					}
				?>
			</div>
	    </div>
    </div> <!-- /container --> 
  <footer>
  	<div class="container">
  		<p>© 2014 Steven Perry</p>
  		<hr>
	  	<ul>
			<li><a href="https://www.facebook.com/steve.perry.7503314" id="facebook"></a></li>
	  		<li><a href="https://github.com/perryst5" id="github"></a></li>
	  		<li><a href="http://linkedin.com/pub/steven-perry/69/595/2a0/" id="linkedin"></a></li>
	  		<li><a href="mailto:perryst5@gmail.com" id="mail"></a></li>
	  		<li><a href="https://twitter.com/steeeveperry" id="twitter"></a></li>
	  	</ul>
    </div>
  </footer>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../js/vendor/jquery-1.10.1.min.js"><\/script>')</script>

        <script src="../assets/js/vendor/bootstrap.min.js"></script>

        <script src="../assets/js/main.js"></script>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		  ga('create', 'UA-46268146-1', 'stevenperry.net');
		  ga('send', 'pageview');
		
		</script>
    </body>
</html>
