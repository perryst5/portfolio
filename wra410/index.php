<?php session_start(); /* Create or resume the user's session */ ?>
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/foundation.css" />
    <link rel="stylesheet" href="css/master.css">
    <script src="js/vendor/modernizr.js"></script>
    <title>Discography Database</title>
  </head>
  <body>
  	<?php 
  		if (isset($_GET['SignOut'])) {
               $SignedIn = false;
               /* Update the session to indicate the user is signed out */
               $_SESSION["SignedIn"] = false;
      	}
    ?>
	<header>
	    <div class="row">
		<div class="large-8 medium-6 small-12 columns">
		   	<a href="index.php">The Discography<br />Database</a>
		</div>
		<div class="large-4 medium-6 hide-for-small-only columns">
     <ul class="button-group right">
      <?php if (!$_SESSION['SignedIn']): ?>
        <li><a href="signup.php" class="button medium">Sign Up</a></li>
        <li><a href="login.php" class="button medium">Log In</a></li>
      <?php else: ?>
        <li><a href="?SignOut" class="button medium">Sign Out</a></li>
      <?php endif; ?>
      </ul>
  </div>
    </div>
    <div class="show-for-small-only row">
  <div class="small-12">
      <ul class="button-group">
        <?php if (!$_SESSION['SignedIn']): ?>
          <li><a href="signup.php" class="button small">Sign Up</a></li>
          <li><a href="login.php" class="button small">Log In</a></li>
        <?php else: ?>
          <li><a href="?SignOut" class="button small">Sign Out</a></li>
        <?php endif; ?>
      </ul>
  </div>
        </header>
    </div>
    <div class="row" id="main-content">
        <div class="medium-3 columns">
	    <ul class="navigation content">
			<li><a href="your_collection.php">View Your Discography</a></li>
			<li><a href="new_data_form.php">Add An Album</a></li>
			<li><a href="#">Update User Profile</a></li>
	    </ul>
  	</div>
  	<div class="medium-9 columns content">
  	    <h1>Welcome to the Discography Database!</h1>
  	    <p>The discography database is great for music lovers who can't seem to keep track of their records and enthusiasts who want to show off their collections.</p>
        <p>This site is designed to list music that you own a physical copy of. Digital downloads don't belong here. (That's iTunes' job.)</p>

      <?php if (!$_SESSION['SignedIn']): ?>
        <p>If you're new, sign up <a href="signup.php">here</a>.</p><br /><br /><br/><br /><br /><br/><br /><br /><br/>
      <?php endif; ?>
    </div>
    </div>
    <footer>
    	<div class="row">
    		<div class="small-12 columns">
    			&copy; <?php echo date("Y") ?> Steven Perry <br/>
    			<a href="http://stevenperry.net">View more of my work.</a>
    		</div>
    	</div>
    </footer>
    <script src="js/vendor/jquery.js"></script>
    <script src="js/foundation.min.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>
