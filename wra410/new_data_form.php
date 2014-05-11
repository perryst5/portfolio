<?php session_start(); /* Create or resume the user's session */ ?>
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/foundation.css" />
    <link rel="stylesheet" href="css/master.css">
    <script src="js/vendor/modernizr.js"></script>
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
      <?php if (!$_SESSION['SignedIn']): ?>
        <div class="large-12 columns content">
         <!-- Not signed in yet; show the form -->
         <h1>Please <a href="login.php">sign in</a> first</h1>
       </div>

      <?php else: ?>
		    <!-- Form for inputting album data -->
		    <form class="content" method="post">
			<h1>Add To Your Collection</h1>
				<div class="row">
				  <div class="large-12 columns">
				    <label>Album Title</label>
				    <input type="text" placeholder="Album Title" name="title" />
				  </div>
				</div>
				<div class="row">
				  <div class="large-4 medium-4 columns">
				    <label>Album Artist</label>
				    <input type="text" placeholder="Album Artist" name="artist" />
				  </div>
				  <div class="large-4 medium-4 columns">
				    <label>Record Label</label>
				    <input type="text" placeholder="Record Label" name="label" />
				  </div>
				  <div class="large-4 medium-4 columns">
					<label>Type of Album</label>
					<select name="format">
					  <option></option>
					  <option value="Vinyl">Vinyl</option>
					  <option value="CD">CD</option>
					  <option value="Casette">Casette</option>
					</select>
				  </div>
				</div>
				<div class="row">
				  <div class="large-3 small-3 columns">
				    <label>Year Published</label>
				    <input type="text" placeholder="YYYY" name="year"></input>
				  </div>
				</div>
				  <!-- needs more table joins
				  <div class="large-3 small-2 columns">
				    <label>On Loan?</label>
				    <input id="checkbox1" type="checkbox"><label for="checkbox1">Yes</label>
				    <input id="checkbox2" type="checkbox"><label for="checkbox2">No</label>
				  </div>
				  <div class="large-5 small-7 columns">
				    <label>If so, to whom?</label>
				    <input type="text" placeholder="John Smith"></input>
				  </div>
				</div> -->
				<div class="row">
				  <div class="large-12 columns">
				    <label>Additional Comments</label>
				    <textarea placeholder="Condition, personal story, etc." name="comment"></textarea>
				  </div>
				</div>
				<div class="row">
				    <div class="small-4 columns">
					<input type="submit" name="submit" value="Submit">
					<input type="reset" value="Clear Form">
				    </div>
				</div>
		    </form>
      <?php endif; ?>
		    <?php
		    	dalConnectToDatabase($dalConfiguration);
		    	/* Get the user's info, so we can display it */
	            $Username = $_SESSION['Username'];
	            $findUser = array(
			      'table'     => 'Users',
			      'look for'  => $Username,
			      'look in'   => 'user_name'
			   );
			   $row = dalRetrieveBySearching($findUser);
			   $user_id = $row['id'];
				if (isset($_POST['submit'])) {
				    $title = $_POST['title'];
				    $artist_name = $_POST['artist'];
				    $format = $_POST['format'];
				    $label = $_POST['label'];
				    $year = $_POST['year'];
				    $comment = $_POST['comment'];
				    /* First add the author */
				    $addAuthor = array(
				      'table'     => 'Artists',
				      'values'    => array(
				         'artist_name'      => $artist_name
				      )
				    );
					$artistID = dalAdd($addAuthor);
	 			    echo "Added $artist_name with ID ", $authorID;

	  			    /* Now we have the ID we need to add the album */
					$addAlbum = array(
				      'table'     => 'Albums',
				      'values'    => array(
				         'album_title'  => $title,
				         'artist_id'    => $artistID,
				         'record_label'	=> $label,
				         'publish_year'	=> $year,
				         'format'	=> $format,
				         'user_id'  => $user_id,
				         'comment'  => $comment
				      )
				   	);
   					$albumID = dalAdd($addAlbum);
   					echo "Added <i>$title<i> with ID ", $albumID;
   					header('Location: your_collection.php');
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
