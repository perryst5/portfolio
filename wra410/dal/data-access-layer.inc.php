<?php
   /***
   WRA 410 Advanced Web Authoring
   Data Access Layer version 1.6.0
   Michael Wojcik (michael.wojcik.msu@gmail.com), April 2014

   This PHP "include file" defines the simple Data Access Layer (DAL)
   that we'll use for WRA 410 projects.
   
   See the README.html that accompanies these files for instructions.
   It's also available at:

      http://ideoplast.org/teaching/wra410/dal/README.html
   ***/

   /***
   "Helper" functions that are used by the DAL but not normally called by
   application scripts. These are all defined as static members of the
   Dal class, so they don't conflict with user functions that happen to
   use the same name.
   ***/
   class Dal {
      /***
      Remove any backquotes from a table or column name, or each item in
      an array of such names. This prevents injection, because such names
      are quoted with backquotes in the SQL commands and since they can't
      contain backquotes, no special characters will have any effect.
      Then put backquotes around the value.

      This modifies the parameter (it's a reference), but also returns it
      for convenience.
      ***/
      public static function backquote(&$toStrip) {
         if (is_array($toStrip)) {
            foreach ($toStrip as &$val) self::backquote($val);
         } else if (is_string($toStrip)) {
            $toStrip = '`' . str_replace('`', '', $toStrip) . '`';
         }
         return $toStrip;
      }

      /***
      Duplicate an array
      ***/
      public static function dupArray(array &$in) {
         $out = array();
         foreach ($in as $key => $value) {
            $out[$key] = $value;
         }
         return $out;
      }

      /***
      Get the optional set of columns for a retrieve operation as a string
      suitable for interpolating into a select command. If no columns are
      specified, "*" is returned.
      ***/
      public static function selectCols(array &$params, $useTableName = false) {
         /* See if columns were specified */
         if (isset($params['columns'])) {
            /* Use PCRE if available */
            if (defined('PCRE_VERSION')) {
               $cols = preg_split('/[, ]+/', $params['columns']);
            } else {
               $cols = split('[, ]+', $params['columns']);
            }
            if (! empty($cols)) {
               /* Collect backquoted columns into a comma-delimited string */
               self::backquote($cols);
               if ($useTableName) {
                  /* Optional prefix */
                  @$prefix = $params['prefix'];
                  $tableName = $params['table'];
                  self::backquote($tableName);
                  foreach ($cols as &$col) {
                     if ($prefix) {
                        /* Add an "as prefixcolumn" phrase */
                        $asName = "${prefix}${col}";
                        self::backquote($asName);
                        $asPhrase = " as $asName";
                     } else {
                        $asPhrase = "";
                     }
                     $col = "$tableName.$col$asPhrase";
                  }
               }
               $out = implode(',', $cols);
            }
         } else {
            /* Columns not specified, so return them all */
            if ($useTableName) {
               $tableName = $params['table'];
               self::backquote($tableName);
               $out = "$tableName.*";
            } else {
               $out = '*';
            }
         }

         /* If we have a join, we need to recurse */
         if (isset($params['also get'])) {
            /* Do we have a single join or multiple? */
            $joins = $params['also get'];
            if (is_array(current($joins))) {
               /* Multiple joins; iterate through them */
               foreach ($joins as $join) {
                  $out .= ',' . self::selectCols($join, true);
               }
            } else {
               $out .= ',' . self::selectCols($joins, true);
            }
         }
         return $out;
      }

      /***
      The column for the left table of a join can be identified using any
      of these names.
      ***/
      protected static $leftNames = array('your', 'left', 'first');

      /* And similarly for the right table */
      protected static $rightNames = array('my', 'right', 'second');

      /***
      Helper for processing joins: create an individual join clause.
      $table is the backquoted name of the left table.
      $jparams is the array of join parameters.
      ***/
      protected static function addJoin($table, array &$jparams) {
         if (!isset($jparams['table'])) die('Join table not specified');

         $yourCol = null;
         foreach (self::$leftNames as $name) {
            if (isset($jparams[$name])) {
               $yourCol = $jparams[$name];
               break;
            }
         }
         if (!$yourCol) die('Join-your column not specified');
         self::backquote($yourCol);

         $myCol = null;
         foreach (self::$rightNames as $name) {
            if (isset($jparams[$name])) {
               $myCol = $jparams[$name];
               break;
            }
         }
         if (!$myCol) die('Join-my column not specified');
         self::backquote($myCol);

         $jtable = $jparams['table']; self::backquote($jtable);

         if (isset($jparams['using'])) {
            /* A relational join, which is handled separately */
            return self::addRelationalJoin(
               $table, $jtable, $yourCol, $myCol, $jparams
            );
         } else {
            return " left join $jtable on ($table.$yourCol = $jtable.$myCol)";
         }
      }

      /***
      Helper for processing joins: create an individual join clause for a
      relational join (a join that uses a third, data-less table that just
      identifies rows in the left and right tables).

      Syntax is:
         'table' => [left table],
         'also get' => {
            'table' => [right table],
            'using' => [relation table],
            'your' => [column in left table],
            'using my' => [corresponding column in relation],
            'using your' => [column in relation],
            'my' => [corresponding column in right table]
         }
      Per normal, "left" and "first" are aliases for "your", and "right"
      and "second" are aliases for "my".
      ***/
      protected static function addRelationalJoin($table, $jtable, $yourCol,
                                                  $myCol, array &$jparams) {
         /* Get the relation columns */
         $yourRelCol = null;
         foreach (self::$leftNames as $name) {
            $colName = 'using ' . $name;
            if (isset($jparams[$colName])) {
               $yourRelCol = $jparams[$colName];
               break;
            }
         }
         if (!$yourRelCol) die('Using-your column not specified');
         self::backquote($yourRelCol);

         $myRelCol = null;
         foreach (self::$rightNames as $name) {
            $colName = 'using ' . $name;
            if (isset($jparams[$colName])) {
               $myRelCol = $jparams[$colName];
               break;
            }
         }
         if (!$myRelCol) die('Using-my column not specified');
         self::backquote($myRelCol);

         $rtable = $jparams['using']; self::backquote($jtable);

         return
            " left join $rtable on ($table.$yourCol = $rtable.$myRelCol)" 
          . " left join $jtable on ($rtable.$yourRelCol = $jtable.$myCol)";
      }

      /***
      Determine if user requested a join; if so, create the join clause for
      the select command. Currently we only do left joins.
      ***/
      public static function joinClause(array &$params) {
         if (!isset($params['also get'])) return "";
         $joins = $params['also get'];
         $table = $params['table']; self::backquote($table);

         $joinClause = '';

         /* See if we have multiple joins */
         if (is_array(current($joins))) {
            /* Multiple joins; iterate through them */
            foreach ($joins as $join) {
               $joinClause .= self::addJoin($table, $join);
               /* Do we have an embedded join? */
               if (isset($join['also get'])) {
                  /* Recurse */
                  $joinClause .= self::joinClause($join);
               }
            }
         } else {
            /* Just a single join to process at this level */
            $joinClause = self::addJoin($table, $joins);
            /* Do we have an embedded join? */
            if (isset($joins['also get'])) {
               $joinClause .= self::joinClause($joins);
            }
         }

         return $joinClause;
      }

      /***
      Create an ORDER BY clause, if appropriate options appear anywhere in
      the parameter array. We search subarrays, breadth first. If multiple
      sort parameters are supplied, which one takes effect is undefined.
      ***/
      public static function orderClause(array &$params) {
         $order="";
         foreach ($params as $key => $value) {
            if (!empty($key) && $key == 'sort') {
               $table = $params['table']; self::backquote($table);
               $col = $params['sort'];    self::backquote($col);
               $order = " order by $table.$col";
               if (isset($params['reverse']) && $params['reverse']) {
                  $order .= " desc";
               }
               break;
            }
            else if (is_array($value)) {
               $order = self::orderClause($value);
               if (! empty($order)) break;
            }
         }
         return $order;
      }

      /***
      Create a LIMIT clause, if either or both limit options ("max rows" and
      "start at") appears in the top level of the parameter array.
      ***/
      public static function limitClause(array &$params) {
         $limit = '';
         @$limitOffset = $params['start at'];
         @$limitNum = $params['max rows'];
         if (!$limitOffset && !$limitNum) return '';
         if (!is_int($limitOffset)) $limitOffset = 0;
         if (!is_int($limitNum)) $limitNum = 4294967295;
         return " limit $limitOffset, $limitNum";
      }

      /***
      Construct the parameterized "where" string and the bind-type string
      for retrieving or modifying a row selected by its primary key. The
      default column name is "ID", but the caller can override that by
      setting the "key name" parameter. The key or ID value itself can be
      specified using "id" or "key value" as the parameter name. If
      "key name" is used, the key value can be a string or an integer;
      otherwise it has to be a positive integer.
      ***/
      public static function getKeyWhereAndType($params,
                                                &$key, &$where, &$bindtype) {
         if (isset($params['key value'])) {
            $key = $params['key value'];
         } else if (isset($params['id'])) {
            $key = $params['id'];
         } else {
            die('Key value or ID not specified');
         }

         if (isset($params['key name'])) {
            $bindtype = is_numeric($key)? 'i' : 's';
            $where = self::backquote($params['key name']) . '=?';
         } else {
            /***
            Using the default ID column. The "id" parameter must be a positive
            integer.
            ***/
            $key = intval($key, 10);
            if ($key <= 0) die('ID must be a positive integer');
            $bindtype = 'i';
            $where = 'id=?';
         }
      }

      /***
      Get the type of a parameter for binding it. This also tells us whether
      we need to send it separately (because it's a blob).
      ***/
      public static function getBindType(&$value) {
         if (is_numeric($value)) {
            if (strchr($value, '.')) {
               $bindType = 'd';  /* number containing a decimal point */
            } else {
               $bindType = 'i';  /* integer */
            }
         } else {
            /***
            TODO: In theory the maximum-length we test against here should be
            the result of querying "SELECT @@global.max_allowed_packet" from
            the database. For the moment I'm using this smaller value because
            it's easier to test and the performance hit won't matter for
            WRA 410.
            ***/
            if (!is_string($value) or strlen($value) < 256) {
               $bindType = 's';  /* short string or other small object */
            } else {
               $bindType = 'b';  /* blob; requires special treatment */
            }
         }

         return $bindType;
      }

      /***
      Given a mysqli prepared select statement that was successfully executed
      (possibly not returning any rows), create a "results" array that
      includes keys for all the returned columns, set to the values of the
      first returned row if there is one (an associative-array of the first
      row); and the key dalNumRows set to the number of rows returned; and the
      key dalAllRows set to an array of associative arrays of each of the rows
      (including the first again).

      Note mysqli only added the get_result and fetch_assoc methods to the
      mysqli_result class with PHP 5.4, and they require the mysqlnd driver.
      So this code uses an approach adapted from a user contribution to the
      PHP manual, which builds a dynamic call to the mysqli bind function.
      ***/
      public static function getSearchResults(mysqli_stmt &$retrieve,
                                              $limited) {
         /***
         Get request metadata, for column names. Note that since this is
         not a buffered query, many of the metadata properties (eg num_rows)
         are invalid.

         TODO: Use the store_result method to buffer the query. That
         generally improves performance and it completes the metadata.
         ***/
         $metadata = $retrieve->result_metadata();

         /***
         Create a parameter array for a dynamic call to the binding function,
         so we can bind keys in currRow to the names of the returned fields
         (columns) in the result set. Then perform the bind. The fields
         array contains the parameters to the bind method; the currROw
         array contains keys (with empty values for now) named after the
         columns.
         ***/
         $fields = array();
         $fields[0] = &$retrieve;
         $currRow = array();
         $idx = 1;
         while ($field = $metadata->fetch_field()) {
            $fields[$idx++] = &$currRow[$field->name];
         }
         call_user_func_array('mysqli_stmt_bind_result', $fields);

         /***
         Get the first row, if the select succeeded. The columns from this
         row are available directly as members of the returned array. If
         you got an ID column back, the array will have an "ID" key with
         the value of the ID from the first returned row.
         ***/
         $gotResults = $retrieve->fetch();
         $result = self::dupArray($currRow);

         /***
         Get all rows. Note this includes the first row again, if there are
         at least two rows, so if your code handles getting multiple rows
         back, it can just look at the dalAllRows array within the returned
         array.
         ***/
         $allRows = array();
         if ($gotResults) {
            $allRows[1] = self::dupArray($currRow);
            $idx = 2;
            while ($retrieve->fetch()) {
               $allRows[$idx++] = self::dupArray($currRow);
            }
            /* Also put the nubmer of rows in the results array */
            $result['dalNumRows'] = $idx - 1;
            /* Also the number of rows we would have gotten without "limit" */
            if ($limited) {
               global $dalConnection;
               @$frResult = $dalConnection->query('select found_rows()');
               @$frRow = $frResult->fetch_row();
               @$result['dalFoundRows'] = $frRow[0];
            } else {
               $result['dalFoundRows'] = $result['dalNumRows'];
            }
         } else {
            $result['dalNumRows'] = 0;
         }
         /* Add all rows to the result as another array */
         $result['dalAllRows'] = $allRows;
            
         return $result;
      }

      /***
      Delete one or more rows, based on the where-clause in the $where
      variable. Note that parameters are trusted and assumed to be clean.
      ***/
      public static function deleteRows($table, $where, $bindtype, $what) {
         global $dalConnection;
         $delete = $dalConnection->prepare(
               "delete from $table where $where"
            )
            or die(
               'Prepare failed: (' . $dalConnection->errno . ') '
               . $dalConnection->error
            );
         $delete->bind_param($bindtype, $what)
            or die(
               'Bind failed: (' . $dalConnection->errno . ') '
               . $dalConnection->error
            );
         $delete->execute()
            or die(
               'Execute failed: (' . $dalConnection->errno . ') ' .
               $dalConnection->error
            );
         return $delete;
      }

      /***
      Send blob data.
      ***/
      public static function sendBlobs($operation,
                                       $numBlobs, &$bindTypes, &$blobValues) {
         for ($paramIdx = 0;
              $numBlobs > 0 && $paramIdx < strlen($bindTypes);
              $paramIdx++) {
            /* See if this parameter is a blob */
            if ($bindTypes[$paramIdx] == 'b') {
               /* Yes; we have to send the data in packets */
/*** DEBUG ***
               echo "<p>Sending blob for parameter $paramIdx ("
                  , strlen($blobValues[$paramIdx])
                  , " bytes)</p>\n";
*** DEBUG ***/
               $operation->send_long_data($paramIdx, $blobValues[$paramIdx])
                  or die(
                     'Sending blob data failed: (' . $dalConnection->errno . ') '
                     . $dalConnection->error
                  );
            }
         }
      }
   }

   $dalDistinct = 'distinct';

   /***
   Connect to the database. This must be called before any other DAL
   functions can be used in the current PHP script.
   ***/
   function dalConnectToDatabase(array &$settings) {
      /***
      We use the global variable $dalConnection to hold the mysqli
      connection instance.
      ***/
      global $dalConnection, $dalDistinct;
      $dalConnection = new mysqli(
         $settings['hostname'],
         $settings['user'],
         $settings['password'],
         $settings['database']
      );
      /***
      The connect_error member doesn't work correctly prior to PHP 5.2.9,
      according to the documentation. Use mysqli_connect_error instead.
      ***/
      $errno = mysqli_connect_errno();
      if ($errno) {
         $error = mysqli_connect_error();
         die("Could not connect to MySQL: ($errno) $error");
      }
      /* Options in the configuration */
      if (isset($settings['distinct'])) $dalDistinct = $settings['distinct'];
   }

   /***
   All the DAL Retreive commands now support simple joins:

   The array can include an "also get" parameter which is an array-of-
   string. It contains a "table" parameter, optionally a "columns"
   parameter, and "your" and "my" parameters which associate the columns
   for the ON clause. For example:

      "also get" => array(
         "table"     => "authors",
         "my"        => "id",          // column in authors table...
         "your"      => "authorId",    // ...matches column in entries table
         // Optional parameters
         "columns"   => "name",
         "prefix"    => "author"       // prepended to column names
      );

   Columns from the joined table can be renamed using the "prefix"
   parameter, which is prepended to their actual names.

   "also get" can also be an array of arrays, where each array looks like
   the above; this left-joins multiple tables to the left table. And it
   can contain an "also get" of its own, which joins a table to the joined
   table. For example:

   $getWithComments = array(
      "table"     => "entries",
      "also get"  => array(
         0  => array(
            "table"     => "authors",
            "my"        => "id",
            "your"      => "authorId",
            "columns"   => "name",
            "prefix"    => "author_"
         ),
         1  => array(
            "table"     => "comments",
            "my"        => "entryId",
            "your"      => "id",
            "columns"   => "text, date",
            "prefix"    => "comment_"
            "also get"  => array(
               "table"     => "users",
               "my"        => "id",
               "your"      => "userId",
               "columns"   => "name",
               "prefix"    => "user_"
            )
         )
      )
   );

   This would return rows with the entry columns plus author_name (from the
   authors table), comment_text and comment_date (for comments on this
   entry, from the comments table), and user_name (for the user who added
   the comment, from the comments table). Note that authors and comments
   are resolved using a relation between entries and each of those tables,
   while users is resolved using a relation between itself and comments.

   Also note which way the relations work between each of the tables:

      entries.authorId -> authors.id
      comments.entryId -> entries.id (so my/yours are reversed for this join)
      comments.userId  -> users.id

   In reality, this probably isn't the query you'd want, because it'll
   return multiple rows for an entry with multiple comments, which is
   unnecessarily verbose. But it illustrates the idea.

   TODO: Support joining through an intermediary relation table. Not sure
   how best to parameterize that.
   ***/

   /***
   Retrieve a single row from a table using an ID, which is taken as the
   value of an auto-increment column named "ID" (or "id", "Id", etc).
   This is typically used when you have an ID value from another table,
   or from a form or URL parameter. Optionally a different key column
   can be specified.
   ***/
   function dalRetrieveByID(array &$params) {
      global $dalConnection, $dalDistinct;

      /* Get parameters from the parameter array */
      if (!isset($params['table'])) die('Table not specified');
      $table = $params['table'];
      Dal::backquote($table);

      /***
      Optionally a key column other than ID may be specified.
      If it is, we allow either an integer or a string value.
      ***/
      $key = ''; $where = ''; $bindtype = '';
      Dal::getKeyWhereAndType($params, $key, $where, $bindtype);

      /***
      If we're doing a join, we need to tell selectCols to prefix columns
      with the table name, and we need to add a join clause.
      ***/
      $haveJoin = isset($params['also get']);

      /***
      See if we have a limit clause. In theory this function should be used
      to return a single, unique row, but that's not enforced, so we have
      full support for limit clauses here. That could actually be useful to
      find conflicting entries in a table that doesn't enforce a constraint:
      do a dalRetrieveByValue with a limit of 1, and check dalFoundRows.
      ***/
      $limit = Dal::limitClause($params);
      $limited = !empty($limit);
      $calcFound = $limited? 'sql_calc_found_rows ' : '';

      /***
      In the database calls below, we attempt the operation, and if it
      fails we use the PHP "die" command to terminate the script with
      an error message. This isn't user-friendly, but it's useful while
      we're developing sites and first learning how to use the DAL.
      ***/
      $retrieve = $dalConnection->prepare(
            "select $dalDistinct $calcFound"
          . Dal::selectCols($params, $haveJoin)
          . " from $table"
          . Dal::joinClause($params)
          . " where $where"
          . Dal::orderClause($params)
          . $limit
         )
         or die(
            'Prepare failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );
      $retrieve->bind_param($bindtype, $key)
         or die(
            'Bind failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );
      $retrieve->execute()
         or die(
            'Execute failed: (' . $dalConnection->errno . ') ' .
            $dalConnection->error
         );
      return Dal::getSearchResults($retrieve, $limited);
   }

   /* An alias for dalRetrieveByID */
   function dalRetrieveByKey($params) {
      if (!isset($params['key name'])) die('Key column name not specified');
      return dalRetrieveByID($params);
   }

   /***
   Retrieve a row from a table by searching a column for some text.
   This is typically used by a simple search function.
   ***/
   function dalRetrieveBySearching(array &$params) {
      global $dalConnection, $dalDistinct;
      /* Get parameters */
      if (!isset($params['table'])) die('Table not specified');
      if (!isset($params['look in'])) die('Look-in not specified');
      if (!isset($params['look for'])) die('Look-for not specified');
      $table = $params['table'];
      Dal::backquote($table);
      $column = $params['look in'];
      Dal::backquote($column);

      /***
      Use the SQL wildcard character "%" before and after the search string.
      Escape any "%" characters that might be in the search string itself.
      ***/
      $searchstr = '%' . str_replace('%', '\%', $params['look for']) . '%';

      /* Process optional compound search */
      $compoundSearchPhrase = '';
      $searchstr2 = '';
      if (isset($params['also for'])) {
         $compoundSearchPhrase =
            isset($params['match']) && strtolower($params['match'] == 'both')?
            ' and' : ' or';
         $alsoIn = isset($params['also in'])?
            $params['also in'] : $params['look in'];
         Dal::backquote($alsoIn);
         $compoundSearchPhrase .= " $table.$alsoIn like ?";
         $searchstr2 =
            '%' . str_replace('%', '\%', $params['also for']) . '%';
      }

      /* See if we have a limit clause */
      $limit = Dal::limitClause($params);
      $limited = !empty($limit);
      $calcFound = $limited? 'sql_calc_found_rows ' : '';

      /* Build the select command */
      $select =
         "select $dalDistinct $calcFound"
       . Dal::selectCols($params, isset($params['also get']))
       . " from $table"
       . Dal::joinClause($params)
       . " where $table.$column like ?"
       . $compoundSearchPhrase
       . Dal::orderClause($params)
       . $limit
       ;
/*** DEBUG ***
      echo
         "<p>Select statement is:</p>\n"
       , "<blockquote>\n"
       , "<tt>$select</tt>\n"
       , "</blockquote>\n"
       ;
*** DEBUG ***/

      /* Try the search */
      $retrieve = $dalConnection->prepare($select)
         or die(
            'Prepare failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );
      if (empty($searchstr2)) {
         $bindResult = $retrieve->bind_param('s', $searchstr);
      } else {
         $bindResult = $retrieve->bind_param('ss', $searchstr, $searchstr2);
      }
      if (!$bindResult) die(
            'Bind failed: (' . $dalConnection->errno . ') ' .
            $dalConnection->error
         );
      $retrieve->execute()
         or die(
            'Execute failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );
      return Dal::getSearchResults($retrieve, $limited);
   }

   /***
   Retrieve all the rows. This is like dalRetrieveBySearching with a value
   for "look for" that matches every row.
   ***/
   function dalRetrieveAll(array &$params) {
      global $dalConnection, $dalDistinct;
      /* Get parameters */
      if (!isset($params['table'])) die('Table not specified');
      $table = $params['table'];
      Dal::backquote($table);

      /* See if we have a limit clause */
      $limit = Dal::limitClause($params);
      $limited = !empty($limit);
      $calcFound = $limited? 'sql_calc_found_rows ' : '';

      /***
      Get all the rows. We don't need a prepared statement, as there are no
      parameters here that can contain user data; but Dal::getSearchResults
      only works with mysqli_stmt objects. So for now just use a prepared
      statement anyway.
      ***/
      $retrieve = $dalConnection->prepare(
            "select $dalDistinct $calcFound"
          . Dal::selectCols($params, isset($params['also get']))
          . " from $table"
          . Dal::joinClause($params)
          . Dal::orderClause($params)
          . $limit
         )
         or die(
            'Prepare failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );
      $retrieve->execute()
         or die(
            'Execute failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );
      return Dal::getSearchResults($retrieve, $limited);
   }


   /***
   Add a row to a table
   ***/
   function dalAdd(array &$params) {
      global $dalConnection;

      /* Check parameter array and get table name */
      if (!isset($params['table'])) die('Table not specified');
      if (!isset($params['values'])) die('Values not specified');
      $table = $params['table'];
      Dal::backquote($table);

      /***
      Extract the columns and their values from the "values" parameter, which
      is an array. The keys are concatenated into a string of backquoted,
      comma-separated column names for the SQL INSERT statement. The values
      are added to an array of parameters for the mysqli_bind_param call.
      ***/
      $columns = '';
      $placeholders = '';  /* will be a series of question marks */
      $bindParams = array(
         '[will be mysqli statement object]',
         ''    /* will be the string of bind types */
      );
      $nullValue = NULL;
      $blobValues = array();
      $paramIdx = 0;    /* used for indexing blob values */
      $numBlobs = 0;
      foreach ($params['values'] as $column => &$value) {
         /* Skip empty values */
         if (empty($column) or empty($value)) continue;
         /* Put commas between column names */
         if (!empty($columns)) $columns .= ',';
         /* Add the column name to the list */
         $columns .= Dal::backquote($column);
         /* Add the placeholder for this value in the prepared statement */
         $placeholders .= empty($placeholders)? '?' : ',?';
         /* Add the bind type for this parameter */
         $bindType = Dal::getBindType($value);
         $bindParams[1] .= $bindType;
         /***
         Add the value as an element in the bind-parameters array, unless
         it's a blob, in which case we have to add a null reference and
         save a reference to the value for later.
         ***/
         if ($bindType != 'b') {
            $bindParams[] = &$value;
         } else {
            $bindParams[] = &$nullValue;
            $blobValues[$paramIdx] = &$value;
            $numBlobs++;
         }
         $paramIdx++;      /* used for addressing blobs later */
      }

/*** DEBUG ***
      echo '<h2>Insert parameters</h2><pre>';
echo "Columns: \"$columns\"\n";
echo "Placeholders: \"$placeholders\"\n";
echo "Values: "; print_r($bindParams);
echo "Number of blobs: $numBlobs\n";
echo "Blob values: "; print_r($blobValues);
      echo '</pre>';
*** DEBUG ***/

      /* Prepare the query */
      $insert = $dalConnection->prepare(
            "insert into $table "
          . "($columns) values "
          . "($placeholders)"
         )
         or die(
            'Prepare failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );

      /* Bind the value list */
      $bindParams[0] = $insert;     /* first parameter is statement object */
      call_user_func_array('mysqli_stmt_bind_param', $bindParams)
         or die(
            'Bind failed: (' . $dalConnection->errno . ') ' .
            $dalConnection->error
         );

      /* If we have any blobs, we have to send them now */
      Dal::sendBlobs($insert, $numBlobs, $bindParams[1], $blobValues);

      /* Execute the operation */
      $insert->execute()
         or die(
            'Execute failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );

/*** DEBUG ***
      echo '<h2>Insert results</h2><pre>';
print_r($insert);
      echo '</pre>';
*** DEBUG ***/

      /***
      If the table has an auto-increment column, and the insert is successful,
      MySQL will give us the ID of the new row, and that's what we want to
      return. If the table doesn't have such a column and the insert is
      successful, we want to return 1 (indicating it succeeded). If the
      insert failed, we want to return 0.

      So we look to see if insert_id is nonzero, and if so we use it. Otherwise
      we look at how many rows were modified - which will be either 1 or 0.

      Also note that both the connection and the statement have an insert_id
      member, but the statement's is not reliable (per the PHP docs).
      ***/
      return $dalConnection->insert_id?
         $dalConnection->insert_id : $insert->affected_rows;
   }


   /***
   Change data in a row. The parameters for this function are like a
   combination of dalRetrieveByID and dalAdd, since you need to tell what
   row you're changing, and what you're changing in it.
   ***/
   function dalUpdate(array &$params) {
      global $dalConnection;

      /* Check parameter array and get table name */
      if (!isset($params['table'])) die('Table not specified');
      if (!isset($params['values'])) die('Values not specified');
      $table = $params['table'];
      Dal::backquote($table);

      /* Get key, where-clause, and bind type for where-clause */
      $key = ''; $where = ''; $keytype = '';
      Dal::getKeyWhereAndType($params, $key, $where, $keytype);

      /***
      Extract the columns and their values from the "values" parameter, which
      is an array. The keys are concatenated into a string of backquoted,
      comma-separated column names for the SQL INSERT statement. The values
      are added to an array of parameters for the mysqli_bind_param call.
      ***/
      $assignments = '';
      /* Allow overriding the null value for empty values */
      $nullValue = isset($params['nullValue'])? $params['nullValue'] : NULL;
      $bindParams = array(
         '[will be mysqli statement object]',
         ''    /* will be the string of bind types */
      );
      $blobNullValue = NULL;  /* Used for blobs */
      $blobValues = array();
      $paramIdx = 0;    /* used for indexing blob values */
      $numBlobs = 0;
      foreach ($params['values'] as $column => &$value) {
         /* Skip empty columns, but NOT empty values */
         if (empty($column)) continue;
         /* Put commas between assignments */
         if (!empty($assignments)) $assignments .= ',';
         /* Add assignment with placeholder */
         $assignments .= ' ' . Dal::backquote($column) . '=?';
         /* Add the bind type for this parameter */
         $bindType = Dal::getBindType($value);
         $bindParams[1] .= $bindType;
         /***
         Add the value as an element in the bind-parameters array. Empty
         values are set to a reference to a null value. Blobs also get
         set to a null value ref, with a reference to the real value set 
         in the blob-values array for later processing.
         ***/
         if (empty($value)) {
            $bindParams[] = &$nullValue;
         } else if ($bindType == 'b') {
            $bindParams[] = &$nullValue;
            $blobValues[$paramIdx] = &$value;
            $numBlobs++;
         } else {
            $bindParams[] = &$value;
         }
         $paramIdx++;      /* used for addressing blobs later */
      }

      /* The last parameter will be the key for the where-clause */
      $bindParams[1] .= $keytype;
      $bindParams[] = &$key;

/*** DEBUG ***
      echo '<h2>Update parameters</h2><pre>';
echo "Assignments: \"$assignments\"\n";
echo "Values: "; print_r($bindParams);
echo "Number of blobs: $numBlobs\n";
echo "Blob values: "; print_r($blobValues);
      echo '</pre>';
*** DEBUG ***/

      /* Prepare the query */
      $update = $dalConnection->prepare(
            "update $table set $assignments where $where"
         )
         or die(
            'Prepare failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );

      /* Bind the value list */
      $bindParams[0] = $update;     /* first parameter is statement object */
      call_user_func_array('mysqli_stmt_bind_param', $bindParams)
         or die(
            'Bind failed: (' . $dalConnection->errno . ') ' .
            $dalConnection->error
         );

      /* If we have any blobs, we have to send them now */
      Dal::sendBlobs($update, $numBlobs, $bindParams[1], $blobValues);

      /* Execute the operation */
      $update->execute()
         or die(
            'Execute failed: (' . $dalConnection->errno . ') '
            . $dalConnection->error
         );

/*** DEBUG ***
      echo '<h2>Update results</h2><pre>';
print_r($update);
      echo '</pre>';
*** DEBUG ***/

      /* Return true if at least one row was changed */
      return $update->affected_rows > 0;
   }


   /***
   Delete a row from a table, by ID or key (like dalRetrieveByID)
   ***/
   function dalDeleteByID(array &$params) {
      global $dalConnection;

      /* Get table from the parameter array */
      if (!isset($params['table'])) die('Table not specified');
      $table = $params['table'];
      Dal::backquote($table);

      /* Get ID or key and related info */
      $key = ''; $where = ''; $bindtype = '';
      Dal::getKeyWhereAndType($params, $key, $where, $bindtype);

      /* Prepare and execute the query */
      $delete = Dal::deleteRows($table, $where, $bindtype, $key);

/*** DEBUG
      echo '<h2>Delete results</h2><pre>';
print_r($delete);
      echo '</pre>';
DEBUG ***/

      /* Return true if at least one row was deleted */
      return $delete->affected_rows > 0;
   }


   /***
   Delete all rows in a table where the specified column includes the
   specified text. Use carefully!
   ***/
   function dalDeleteBySearching(array &$params) {
      global $dalConnection;
      /* Get parameters */
      if (!isset($params['table'])) die('Table not specified');
      if (!isset($params['look in'])) die('Look-in not specified');
      if (!isset($params['look for'])) die('Look-for not specified');
      $table = $params['table'];
      Dal::backquote($table);
      $column = $params['look in'];
      Dal::backquote($column);

      /* See dalRetrieveBySearching for an explanation of the following */
      $where = "$column like ?";
      $what = '%' . str_replace('%', '\%', $params['look for']) . '%';

      /* Prepare and execute the query */
      $delete = Dal::deleteRows($table, $where, 's', $what);

/*** DEBUG 
      echo '<h2>Delete results</h2><pre>';
print_r($delete);
      echo '</pre>';
DEBUG ***/

      /* Return the number of rows that were deleted */
      return $delete->affected_rows;
   }


   /***
   Convenience functions for managing users
   ***/
   function dalAddUser(array &$params) {
      $adduser = array();

      /***
      Look for the username. It may be specified at the top level or in values,
      and at the top level may be called "username" even if that's not the
      name of the associated column.
      ***/
      $username = '';
      $nameCol = isset($params['username column'])?
         $params['username column'] : 'username';
      if (isset($params[$nameCol])) {
         $username = $params[$nameCol];
      } else if ($nameCol != 'username' && isset($params['username'])) {
         $username = $params['username'];
      } else if (isset($params['values']) &&
                 isset($params['values'][$nameCol])) {
         $username = $params['values'][$nameCol];
      }
      if (empty($username)) die('Username not specified');

      /***
      Similarly for password: It may be in the top level as "password" or
      as the value of the password column name, or under values as the
      value of the password column name.
      ***/
      $password = '';
      $passCol = isset($params['password column'])?
         $params['password column'] : 'password';
      if (isset($params[$passCol])) {
         $password = $params[$passCol];
      } else if ($passCol != 'password' && isset($params['password'])) {
         $password = $params['password'];
      } else if (isset($params['values']) &&
                 isset($params['values'][$passCol])) {
         $password = $params['values'][$passCol];
      }
      if (empty($password)) die('Password not specified');

      /* Set up parameters for add, using input and defaults */
      $adduser['table'] = isset($params['table'])? $params['table'] : 'users';
      $adduser['values'] = isset($params['values'])?
         Dal::dupArray($params['values']) : array();
      $adduser['values'][$nameCol] = $username;
      $adduser['values'][$passCol] = md5($password);
/*** DEBUG ***
echo '<h2>AddUser Parameters</h2><pre>';
print_r($adduser);
echo '</pre>';
*** DEBUG ***/

      /***
      See if username is already in use. This would normally be enforced by
      a UNIQUE constraint on the table (and it may be), but making an
      explicit check here means the student doesn't have to remember to
      set that constraint; and it also gives us a place for better error
      reporting in a future version of the DAL.
      ***/
      $result = dalRetrieveByKey(array(
         'table' => $adduser['table'],
         'key name' => $nameCol,
         'key value' => $username
      ));
      if ($result['dalNumRows'] > 0) return 0;

      return dalAdd($adduser);
   }

   function dalVerifyUser(array &$params) {
      /* TODO: Refactor code duplicated from dalAddUser */
      /* Look for the username */
      $username = '';
      $nameCol = isset($params['username column'])?
         $params['username column'] : 'username';
      if (isset($params[$nameCol])) {
         $username = $params[$nameCol];
      } else if ($nameCol != 'username' && isset($params['username'])) {
         $username = $params['username'];
      } else if (isset($params['values']) &&
                 isset($params['values'][$nameCol])) {
         $username = $params['values'][$nameCol];
      }
      if (empty($username)) die('Username not specified');

      /* Similarly for password */
      $password = '';
      $passCol = isset($params['password column'])?
         $params['password column'] : 'password';
      if (isset($params[$passCol])) {
         $password = $params[$passCol];
      } else if ($passCol != 'password' && isset($params['password'])) {
         $password = $params['password'];
      } else if (isset($params['values']) &&
                 isset($params['values'][$passCol])) {
         $password = $params['values'][$passCol];
      }
      if (empty($password)) die('Password not specified');

      /* Get or default table and column names */
      $table = isset($params['table'])? $params['table'] : 'users';
      $nameCol = isset($params['username column'])?
         $params['username column'] : 'username';
      if (empty($username)) die('Username not specified');
      $passCol = isset($params['password column'])?
         $params['password column'] : 'password';

      /* Retrieve user record */
      $result = dalRetrieveByKey(array(
         'table' => $table,
         'key name' => $nameCol,
         'key value' => $username
      ));
      if ($result['dalNumRows'] == 0) return false;

      /* Compare password hashes */
      if (md5($password) != $result[$passCol]) return false;

      /***
      Set session values using "dalUser " . column-name. This is only
      effective cross-page if the calling page did a session_start().
      ***/
      if (!isset($_SESSION)) $_SESSION = array();
      foreach ($result['dalAllRows'][1] as $column => $value) {
         /* Don't include the password hash */
         if ($column != $passCol) {
            $_SESSION["dalUser $column"] = $value;
         }
      }

      return true;
   }

   function dalChangeUserPassword(array &$params) {
      $updateUser = array();

      /* TODO: Refactor code duplicated from dalAddUser */

      /* Look for the username. See dalAddUser */
      $username = '';
      $nameCol = isset($params['username column'])?
         $params['username column'] : 'username';
      if (isset($params[$nameCol])) {
         $username = $params[$nameCol];
      } else if ($nameCol != 'username' && isset($params['username'])) {
         $username = $params['username'];
      } else if (isset($params['values']) &&
                 isset($params['values'][$nameCol])) {
         $username = $params['values'][$nameCol];
      }
      if (empty($username)) die('Username not specified');

      /* Similarly for password */
      $password = '';
      $passCol = isset($params['password column'])?
         $params['password column'] : 'password';
      if (isset($params[$passCol])) {
         $password = $params[$passCol];
      } else if ($passCol != 'password' && isset($params['password'])) {
         $password = $params['password'];
      } else if (isset($params['values']) &&
                 isset($params['values'][$passCol])) {
         $password = $params['values'][$passCol];
      }
      if (empty($password)) die('Password not specified');

      /* Set up parameters for update, using input and defaults */
      $updateUser['table'] = isset($params['table'])?
         $params['table'] : 'users';
      $updateUser['values'] = isset($params['values'])?
         Dal::dupArray($params['values']) : array();
      $updateUser['key name'] = $nameCol;
      $updateUser['key value'] = $username;
      $updateUser['values'][$passCol] = md5($password);
/*** DEBUG ***
echo '<h2>UpdateUser Parameters</h2><pre>';
print_r($updateuser);
echo '</pre>';
*** DEBUG ***/

      /* Update user */
      return dalUpdate($updateUser);
   }
?>

