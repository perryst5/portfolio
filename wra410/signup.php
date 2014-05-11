<?php session_start(); /* Create or resume the user's session */ ?>
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/foundation.css" />
    <link rel="stylesheet" href="css/master.css">
    <script src="js/vendor/modernizr.js"></script>
    <script> 
    	var height = document.innerHeight;
    	x = height - 320;
    	document.getElementById('middle').style.height = x;
    </script>
    <title>Add to Collection</title>
  </head>
  <body>
  	<?php
      require_once "dal/data-access-configuration.inc.php";
      require_once "dal/data-access-layer.inc.php";
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
    <div class="row middle" id="main-content">
        <div class="medium-3 columns">
		    <ul class="navigation content">
				<li><a href="your_collection.php">View Your Discography</a></li>
				<li><a href="new_data_form.php">Add An Album</a></li>
				<li><a href="update.php">Update User Profile</a></li>
		    </ul>
		</div>
		<div class="medium-9 columns">
		    <!-- Form for inputting album data -->
		    <form class="content" method="post">
			<h1>Sign Up!</h1>
				<div class="row">
				  <div class="large-12 columns">
				    <label>Username</label>
				    <input type="text" placeholder="Username" name="username" />
				  </div>
				</div>
				<div class="row">
				  <div class="large-4 medium-4 columns">
				    <label>First Name</label>
				    <input type="text" placeholder="First Name" name="firstname" />
				  </div>
				  <div class="large-4 medium-4 columns">
				    <label>Last Name</label>
				    <input type="text" placeholder="Last Name" name="lastname" />
				  </div>
				  <div class="large-4 medium-4 columns">
					<label>Age</label>
					<input type="text" placeholder="Age" name="age"></input>
				  </div>
				</div>
				<div class="row">
				  <div class="large-4 small-4 columns">
				    <label>Email Address</label>
				    <input type="text" placeholder="Email Address" name="email"></input>
				  </div>
				  <div class="large-4 small-4 columns">
				    <label>Password</label>
				    <input type="password" placeholder="Password" name="password"></input>
				  </div>
				  <div class="large-4 small-4 columns">
				    <label>Confirm Password</label>
				    <input type="password" placeholder="Confirm Password" name="confirmpassword"></input>
				  </div>
				</div>
				<div class="row">
				    <div class="small-4 columns">
					<input type="submit" name="submit" value="Submit">
					<input type="reset" value="Clear Form">
				    </div>
				</div>
		    </form>
		    <?php
		    	dalConnectToDatabase($dalConfiguration);
				if (isset($_POST['submit'])) {
				    $username = $_POST['username'];
				    $firstname = $_POST['firstname'];
				    $lastname = $_POST['lastname'];
				    $age = $_POST['age'];
				    $email = $_POST['email'];
				    $password = $_POST['password'];
				    $cpassword = $_POST['confirmpassword'];
				    if ($password == $cpassword) {
					    /* Add the User! */
						$addUser = array(
					      'table'     => 'Users',
					      'values'    => array(
					         'user_name'  	=> $username,
					         'first_name'   => $firstname,
					         'last_name'	=> $lastname,
					         'age'			=> $age,
					         'email'		=> $email,
					         'password'		=> $password
					      )
					   	);
						dalAdd($addUser);
						/* Sign the user in! */
		                $SignedIn = true;
		                /* Save this in the session */
		                $_SESSION['SignedIn'] = true;
		                /* Also remember the user's name */
		                $_SESSION['Username'] = $username;
	   					echo "Added <i>$username</i> to the database. Welcome $firstname $lastname!";
	   				} else {
	   					echo "Passwords don't match!";
	   				}
				}
			?>

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
