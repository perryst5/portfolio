<?php
  require_once "dal/data-access-configuration.inc.php";
  require_once "dal/data-access-layer.inc.php";
  dalConnectToDatabase($dalConfiguration);
  
  $getItem = array(
      'table'     => 'Artists',
      'id'        => 1
  );
  $row = dalRetrieveByID($getItem);
?>

	    <pre>
   			<?php print_r($row) ?>
   		</pre>
   		
   		<?php
		   $findItem = array(
		      'table'     => 'Albums',
		      'look for'  => 'Impossible',
		      'look in'   => 'album_title'
		   );
   			$row2 = dalRetrieveBySearching($findItem);
		?>
		
		 This is the result of a search:
		   <pre>
		  		<?php print_r($row2) ?>
		   </pre>
		   
		
<p>Or, a more friendly way of looking at this. (I can't put the info into my mockups because we haven't covered table joins yet.)</p>

<p>Artist entry number 1 is: <i><?php echo $row['artist_name'] ?></i></p>
<p>A record returned when 'Impossible' is searched for: <i><?php echo $row2['album_title'] ?>, <?php echo $row2['record_label'] ?>, <?php echo $row2['publish_year'] ?></i></p>