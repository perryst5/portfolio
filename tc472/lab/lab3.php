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
        <link rel="stylesheet" href="../../css/master.css">

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
            <li><a href="../../index.html">Home</a></li>
            <li><a href="../../page/work.html">Work</a></li>
            <li><a href="../../page/resume.html">Resume</a></li>
            <li><a href="../../page/contact.html">Contact</a></li>
          </ul>
          <a class="navbar-brand pull-right hidden-xs" href="../../index.html">Steven Perry</a>
        </div><!--/.navbar-collapse -->
      </div>
    </div>
    <div class="container">
		<div class="text-center"><h1>Lab 3</h1></div>
		<hr class="title">
		<div class="row">
			<div class="col-xs-6 col-sm-offset-3 col-md-offset-3 col-lg-offset-3 text-center well">
				<form method="post">
					<div class="form-group">
						<label for="firstname">First Name: </label><input type="text" name="firstname" class="form-control" style="width:50%;text-align:center;margin:0 auto;" value="<?php echo $_POST['firstname'] ?>" >
					</div>
					<div class="form-group">
						<label for="lastname">Last Name: </label><input type="text" name="lastname" class="form-control" style="width:50%;text-align:center;margin:0 auto;" value="<?php echo $_POST['lastname'] ?>">
					</div>
					<div class="form-group">
						<label for="mail">E-mail: </label><input type="text" name="mail" class="form-control" style="width:50%;text-align:center;margin:0 auto;" value="<?php echo $_POST['mail'] ?>">
					</div>
					<div class="form-group">
						<label for="sex">Sex</label><br />
						<input type="radio" name="sex" value="male" <?php if ($_POST['sex'] == "male") { echo "checked=\"checked\"";} ?>> Male<br />
						<input type="radio" name="sex" value="female" <?php if ($_POST['sex'] == "female") { echo "checked=\"checked\"";} ?>> Female
					</div>
					<label for="price">Price</label><br />
					<select name="price">
						<option></option>
						<option value="inexpensive">Inexpensive</option>
						<option value="resonable">Reasonable</option>
						<option value="expensive">Expensive</option>
						<option value="unaffordable">Unaffordable</option>
					</select><br /><br />
					<label for="feedback">Provide Feedback</label><br />
					<div><textarea name="feedback" cols="30" rows="4" class="form-control" style="width:50%;margin:0 auto;" value=" value="<?php echo $_POST['feedback']; ?>"></textarea></div><br />
					<input type="submit" name="submit" value="Submit" class="btn btn-submit">
				</form>
				<br><br>
				<?php 
				    $firstname = $_POST['firstname'];
				    $lastname = $_POST['lastname'];
				    $mail = $_POST['mail'];
				    $sex = $_POST['sex'];
				    $price = $_POST['price'];
				    $feedback = $_POST['feedback'];
				    
					if (isset($_POST['submit']))
						{  
						
						   if (!$firstname) { 
						   echo "<p>Please enter your first name.</p>";
						   } else {
						   	echo "<p>Your first name is $firstname.</p>";
						   }
						   if (!$lastname) { 
						   echo "<p>Please enter your last name.</p>";
						   } else {
						   	echo "<p>Your last name is $lastname.</p>";
						   }
						   if (!$mail) { 
						   echo "<p>Please enter your email address.</p>";
						   } else {
						   	echo "<p>Your email is $mail</p>";
						   }
						   if (!$sex) { 
						   echo "<p>Please select your sex.</p>";
						   } else {
						   	echo "<p>You selected $sex as your sex.</p>";
						   }
						   if (!$price) { 
						   echo "<p>Please select your price.</p>";
						   } else {
						   	echo "<p>You selected $price as the price.</p>";
						   }
						   if (!$feedback) { 
						   echo "<p>Please enter your feedback</p>";
						   } else {
						   	echo "<p>Your feedback is: <blockquote>$feedback</blockquote></p>";
						   }
						}   
					else { 
						echo "<p>Please complete the form above and click \"Submit\".</p>";
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