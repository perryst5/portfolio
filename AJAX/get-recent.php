<?php
   require_once '../wra410/dal/data-access-layer.inc.php';
   require_once '../wra410/dal/data-access-configuration.inc.php';
   dalConnectToDatabase($dalConfiguration);
   /***
   Get the most recent addition to the table. We do this by telling
   dalRetrieveAll to only retrieve one row, and to order the rows from
   highest ID to lowest ID. That will give us the row with the highest
   ID.
   ***/
   $search = array(
      'table' => 'entries',
      'sort' => 'id',
      'reverse' => true,
      'max rows' => 1
   );
   $row = dalRetrieveAll($search);
   $author = $row['author'];
   $title = $row['title'];
   echo "<td>$author</td> <td><i>$title</i></td>";
?>