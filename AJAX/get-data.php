<?php
   require_once '../wra410/dal/data-access-layer.inc.php';
   require_once '../wra410/dal/data-access-configuration.inc.php';
   dalConnectToDatabase($dalConfiguration);
   /* Get the search string */
   $searchString = $_GET['search'];
   /* Search the table for it */
   $search = array(
      'table' => 'entries',
      'look for' => $searchString,
      'look in' => 'title'
   );
   $rows = dalRetrieveBySearching($search);
   /* We'll only return the first row found */
   $author = $rows['author'];
   $title = $rows['title'];
   echo "<td>$author</td> <td><i>$title</i></td>";
?>