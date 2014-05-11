<?php session_start(); /* Create or resume the user's session */ ?>
<!DOCTYPE html>
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
      require_once "dal/data-access-configuration.inc.php";
      require_once "dal/data-access-layer.inc.php";
      /* Connect to Database */
	    dalConnectToDatabase($dalConfiguration);
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
			<li><a href="update.php">Update User Profile</a></li>
	    </ul>
	</div>
	<div class="medium-9 columns">
	  <!--
         If the user is not signed in, we'll display the sign-in form.
         If the user has signed in, we'll display the link to sign out.
      -->

      <?php
         /***
            See if the user has already signed in, by looking in the
            $_SESSION array for a value we set when the sign-in form
            is submitted and checked.
         ***/
         $SignedIn = @$_SESSION["SignedIn"];
         if ($SignedIn) {
            /***
               The user has signed in. Did they just ask to be signed
               out? Look to see if we were sent a parameter named
               "SignOut".
            ***/
            if (isset($_GET['SignOut'])) {
               $SignedIn = false;
               /* Update the session to indicate the user is signed out */
               $_SESSION["SignedIn"] = false;
               /* Tell the user */
               echo '<p><strong>You are now signed out</strong></p>';
            }
         } else {
            /***
               The user has not signed in yet. Did they just submit the
               sign-in form?
            ***/
            if (isset($_POST['SignInForm'])) {
               /* Check username and password against database values */

               /* Create array for table Users */
               $getUser = array(
                 'table' => 'Users',
                 'look in' => 'user_name',
                 'look for'=> $_POST['username']
               );
               // Search array for username
              $result = dalRetrieveBySearching($getUser);
              // Search result for password
              if (in_array('1', $result)){
                if (in_array($_POST['password'], $result)) {
                  /* Sign the user in! */
                  $SignedIn = true;
                  /* Save this in the session */
                  $_SESSION['SignedIn'] = true;
                  /* Also remember the user's name */
                  $Username = $_POST['username'];
                  $_SESSION['Username'] = $Username;
               } else {
                 echo '<p><strong>Wrong username or password</strong></p>';
                }
              }
			        if (!$result) {
				          die('Invalid query: ' . mysql_error());
				      }
            }
         }
      ?>

      <?php if (! $SignedIn): ?>

         <!-- Not signed in yet; show the form -->
         <div class="content">
	         <h1>You must sign in first!</h1>
	         <form action="#" method="post">
	            <label>
	               Username:
	            </label><input name="username" size="10" type="text"><br>
	            <label>
	               Password:
	            </label><input name="password" size="10" type="password"><br>
	            <input value="Sign in" name="SignInForm" type="submit">
	         </form>
	     </div>

      <?php else: ?>

         <!-- The user is signed in -->
         <?php
            /* Get the user's info, so we can display it */
            $Username = $_SESSION['Username'];
            $findUser = array(
		      'table'     => 'Users',
		      'look for'  => $Username,
		      'look in'   => 'user_name'
		   );
		   $row = dalRetrieveBySearching($findUser);
		   $first = $row['first_name'];
		   $last = $row['last_name'];
		   $age = $row['age'];
		   $mail = $row['email'];
		   $user_id = $row['id'];
         ?>
	        <form class="content" method="post">
	         <h1>Update your profile!</h1>
				<div class="row">
				  <div class="large-12 columns">
				    <label>Username</label>
				    <input type="text" placeholder="Username" name="username" value="<?php echo $Username ?>" />
				  </div>
				</div>
				<div class="row">
				  <div class="large-4 medium-4 columns">
				    <label>First Name</label>
				    <input type="text" placeholder="First Name" name="firstname" value="<?php echo $first ?>" />
				  </div>
				  <div class="large-4 medium-4 columns">
				    <label>Last Name</label>
				    <input type="text" placeholder="Last Name" name="lastname" value="<?php echo $last ?>" />
				  </div>
				  <div class="large-4 medium-4 columns">
					<label>Age</label>
					<input type="text" placeholder="Age" name="age" value="<?php echo $age ?>"></input>
				  </div>
				</div>
				<div class="row">
				  <div class="large-4 medium-4 columns">
				    <label>Email Address</label>
				    <input type="text" placeholder="Email Address" name="email" value="<?php echo $mail ?>"></input>
				  </div>
				  <div class="large-4 medium-4 columns">
				    <label>Password</label>
				    <input type="password" placeholder="Password" name="password"></input>
				  </div>
				  <div class="large-4 medium-4 columns">
				    <label>Confirm Password</label>
				    <input type="password" placeholder="Confirm Password" name="confirmpassword"></input>
				  </div>
				</div>
				<div class="row">
				    <div class="small-4 columns">
					<input type="submit" name="submit" value="Submit">
				    </div>
				</div>
			</form>
	  <?php 
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
				$editUser = array(
			      'table'     => 'Users',
			      'id'		  => $user_id,
			      'values'    => array(
			         'user_name'  	=> $username,
			         'first_name'   => $firstname,
			         'last_name'	=> $lastname,
			         'age'			=> $age,
			         'email'		=> $email,
			         'password'		=> $password
			      )
			      
			   	);
	   			dalUpdate($editUser);
				echo "Updated <i>$username</i>. Thanks $firstname $lastname!";
			} else {
				echo "Passwords don't match!";
			}
	   	  }
   	  ?>
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
