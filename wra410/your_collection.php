<?php session_start(); /* Create or resume the user's session */ ?>
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/foundation.css" />
    <link rel="stylesheet" href="css/master.css">
    <script src="js/vendor/modernizr.js"></script>
    <title>Your Collection</title>
<script type="text/javascript">
      /***
      This function looks to see if the search text exists inside the
      given element.
      ***/
      function searchElement(elem, text, ignoreCase) {
         /***
         A better (more elegant and more accurate) method would be to
         check every text node within the tree that starts at elem.
         But this is simpler and clearer, and works for our purposes,
         aside from incorrectly finding things like tag names.
         ***/
         /* Get the element and its children as a string of HTML */
         var elemContents = elem.innerHTML;
         /* To ignore case, we'll just make everything lower-case */
         if (ignoreCase) elemContents = elemContents.toLowerCase();
         /* Search that HTML for the text we're looking for */
         var match = elemContents.indexOf(text);
         /* If match is not negative, we found the text */
         if (match < 0) {
            return false;
         } else {
            return true;
         }
      }

      /***
      Go through the rows of the table. If a row contains the search text,
      reveal it; otherwise hide it.
      ***/
      function searchTable(table, text, ignoreCase) {
         /* Process each row of the table */
         for (var i=0, row; row = table.rows[i]; i++) {
            /* See if this row contains the text */
            var matches = searchElement(row, text, ignoreCase);
            /* Hide or reveal it */
            if (matches) {
               row.style.display = null;  /* go back to default display style */
            } else {
               row.style.display = "none";   /* hide it */
            }
         }
      }

      /***
      Get the search string from the form, find the table, and hide or
      reveal rows based on the string.
      ***/
      function search() {
         /* Get the search string from the form */
         var form = document["searchForm"];
         var searchText = form.elements["searchText"].value;
         /* Are we ignoring case? */
         var ignoreCase = form.elements["ignoreCase"].checked;
         if (ignoreCase) searchText = searchText.toLowerCase();
         /* Find the table */
         var table = document.getElementById("entries");
         /* Do the search */
         searchTable(table, searchText, ignoreCase);
         return false;  /* don't submit the form to the server */
      }
   </script>
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
		<!-- Consistant Header across all pages -->
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
    <!-- Navigation -->
    <div class="row" id="main-content">
        <div class="medium-3 columns">
	    <ul class="navigation content">
		<li><a href="your_collection.php">View Your Discography</a></li>
		<li><a href="new_data_form.php">Add An Album</a></li>
		<li><a href="update.php">Update User Profile</a></li>
	    </ul>
	</div>
	<div class="medium-9 columns content">
	<?php if (!$_SESSION['SignedIn']): ?>
         <!-- Not signed in yet; show the form -->
         <h1>Please <a href="login.php">sign in</a> first</h1>
    <?php else: ?>
    <!-- The search form -->
   <form name="searchForm" action="#" method="POST" onsubmit="return search()" onkeyup="search()">
      <p><label>Search for <input type="text" name="searchText"></label>
         <label>Ignore case <input type="checkbox" name="ignoreCase"></label></p>
   </form>
	<?php

		/* Connect to Database */
		dalConnectToDatabase($dalConfiguration);

		/* Create array for table Albums */
		$getAlbums = array('table' => 'Albums');

		/* Get all data from albums table */
		$allAlbums = dalRetrieveAll($getAlbums);
	?>
	  <h1>Your Collection</h1>
	    <table id="entries">
	      <thead>
    			<tr>
    			  <th width="200">Album Title</th>
    			  <th width="100">Album Artist</th>
    			  <th width="150">Record Label</th>
    			  <th width="150">Type</th>
    			  <th width="150">Year Published</th>
    			  <!-- Requiring more table joins that will be added later
    			  <th width="150">Loan Status</th> -->
    			  <th width="150">Comments</th>
    			  <th>Delete!</th>
    			</tr>
	      </thead>
	      <tbody>
		    <?php
				/* For each row, get artist ID */
				foreach ($allAlbums['dalAllRows'] as $album) {
				/* Search Artist table for ids that match the artist id in the albums table */
				$getArtist = array(

				'table' => 'Artists',

				'id' => $album['artist_id']

				);

				$artist = dalRetrieveByID($getArtist);
				/* set artist name */
				$artist_name = $artist['artist_name'];
				/* set album title */
				$title = $album['album_title'];
				/* set record label */
				$label = $album['record_label'];
				/* set format */
				$format = $album['format'];
				/* set year */
				$year = $album['publish_year'];
				/* set comment */
				$comment = $album['comment'];
				/* set albumId */
				$aid = $album['id'];
				
				/* Get the user's info, so we can display it */
	            $Username = $_SESSION['Username'];
	            $findUser = array(
			      'table'     => 'Users',
			      'look for'  => $Username,
			      'look in'   => 'user_name'
			   );
			   $row = dalRetrieveBySearching($findUser);
			   $user_id = $row['id'];
				if ($album['user_id'] == $user_id ){
					echo "
						<tr>
						  <td>$title</td>
						  <td>$artist_name</td>
						  <td>$label</td>
						  <td>$format</td>
						  <td>$year</td>
						  <td>$comment</td>
						  <td><a href='?id=$aid'>X</a></td>
						</tr>";
					}
				}
				if ($_GET['id']) {
					$removeRow = array(
				      'table'     => 'Albums',
				      'id'        => $_GET['id']
				    );
		            dalDeleteById($removeRow);
		            // redirect to the same page without the GET data
		            echo "<script>window.location.href = window.location.protocol +'//'+ window.location.host + window.location.pathname;</script>";
		      	}
			?>
	      </tbody>
	    </table>
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
