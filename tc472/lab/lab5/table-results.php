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
        <link rel="stylesheet" href="../../../css/master.css">
		<style>td, th {border: 1px solid #999;padding: 2px;}</style>
        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand visible-xs" href="#">Steven Perry</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="../../../index.html">Home</a></li>
            <li><a href="../../../page/work.html">Work</a></li>
            <li><a href="../../../page/resume.html">Resume</a></li>
            <li><a href="../../../page/contact.html">Contact</a></li>
          </ul>
          <a class="navbar-brand pull-right hidden-xs" href="../../../index.html">Steven Perry</a>
        </div><!--/.navbar-collapse -->
      </div>
    </div>
    <div class="container">
		<div class="text-center"><h1>Lab 5 - Table</h1></div>
		<hr class="title">
		<div class="row">
			<div class="col-xs-6 col-sm-offset-3 col-md-offset-3 col-lg-offset-3 text-center well">
				<?php
				
					require("connect.php");
				
					$SQL="SELECT * FROM lab5";
					$result = mysql_query($SQL) or die("could not complete your query");
					
						echo "<table><tr><th>Name</th><th>Email</th><th>Sex</th><th>Price</th><th>Feedback</th></tr>";
							while ($row = mysql_fetch_array($result))  {
							$firstname = $row['firstname'];
							$lastname = $row['lastname'];
							$mail = $row['mail'];
							$sex = $row['sex'];
							$price = $row['price'];
							$feedback = $row['feedback'];
				
							echo "<tr><td>$firstname $lastname</td><td>$mail</td><td>$sex</td><td>$price</td><td>$feedback</td></tr>";
							} 
						echo "</table>";
				
				mysql_close($connect);
				
				?> 
				<p><a href="../lab5.php">Back to the Form!</a></p>
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
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
        <script src="js/vendor/bootstrap.min.js"></script> 
        <script src="js/main.js"></script>
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
