<?php
   /***
   WRA 410 Advanced Web Authoring
   Data Access Layer
   Michael Wojcik (michael.wojcik.msu@gmail.com), February 2014

   This PHP "include file" defines the simple Data Access Layer (DAL)
   that we'll use for WRA 410 projects.
   
   See the README.html that accompanies these files for instructions.
   It's also available at:

      http://ideoplast.org/teaching/wra410/dal/README.html
   ***/

   /***
   Connect to the database. This must be called before any other DAL
   functions can be used in the current PHP script.
   ***/
   function dalConnectToDatabase(array $settings) {
      /***
      We use the global variable $dalConnection to hold the mysqli
      connection instance.
      ***/
      global $dalConnection;
      $dalConnection = new mysqli(
         'localhost',
         $settings['user'],
         $settings['password'],
         $settings['database']
      );
      if ($dalConnection->connect_errno) {
         die('Could not connect to MySQL ' . $dalConnection->connect_errno);
      }
   }

   /***
   Retrieve a single row from a table using an ID, which is taken as the
   value of an auto-increment column named "ID" (or "id", "Id", etc).
   This is typically used when you have an ID value from another table,
   or from a form or URL parameter.
   ***/
   function dalRetrieveByID(array $params) {
      global $dalConnection;

      /* Get parameters from the parameter array */
      if (!isset($params['table'])) die('Table not specified');
      if (!isset($params['id'])) die('ID not specified');
      /* Remove any backquotes from table name; this prevents injection */
      $table = str_replace('`', '', $params['table']);
      /* ID must be a positive integer */
      $id = intval($params['id'], 10);
      if ($id <= 0) die('ID must be a positive integer');

      /***
      In the database calls below, we attempt the operation, and if it
      fails we use the PHP "die" command to terminate the script with
      an error message. This isn't user-friendly, but it's useful while
      we're developing sites and first learning how to use the DAL.
      ***/
      $retrieve = $dalConnection->prepare("select * from `$table` where id=?")
         or die(
            'Prepare failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );
      $retrieve->bind_param('i', $id)
         or die(
            'Bind failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );
      $retrieve->execute()
         or die(
            'Execute failed: (' . $dalConnection->errno . ') ' .
            $dalConnection->error
         );
      $res = $retrieve->get_result()
         or die(
            'Get-result failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );

      /***
      Note we have to put the result of fetch_assoc in a variable, not
      just return it, or we return a boolean rather than an array.
      ***/
      $row = $res->fetch_assoc()
         or die(
            'Fetch failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );
      return $row;
   }

   /***
   Retrieve a row from a table by searching a column for some text.
   This is typically used by a simple search function.
   ***/
   function dalRetrieveBySearching(array $params) {
      global $dalConnection;
      /* Get parameters */
      if (!isset($params['table'])) die('Table not specified');
      if (!isset($params['look in'])) die('Look-in not specified');
      if (!isset($params['look for'])) die('Look-for not specified');
      /* Remove any backquotes from table and column names */
      $table = str_replace('`', '', $params['table']);
      $column = str_replace('`', '', $params['look in']);

      /***
      Use the SQL wildcard character "%" before and after the search string.
      Escape any "%" characters that might be in the search string itself.
      ***/
      $searchstr =
         '%' . str_replace('%', '\%', $params['look for']) . '%';

      /* Try the search */
      $retrieve = $dalConnection->prepare
         ("select * from `$table` where `$column` like ?")
         or die(
            'Prepare failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );
      $retrieve->bind_param('s', $searchstr)
         or die(
            'Bind failed: (' . $dalConnection->errno . ') ' .
            $dalConnection->error
         );
      $retrieve->execute()
         or die(
            'Execute failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );
      $res = $retrieve->get_result()
         or die(
            'Get-result failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );
      $row = $res->fetch_assoc()
         or die(
            'Fetch failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );
      return $row;
   }
?>

