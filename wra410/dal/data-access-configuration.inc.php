<?php
   /***
   WRA 410 Advanced Web Authoring
   Data Access Layer - Configuration
   Michael Wojcik (michael.wojcik.msu@gmail.com), February 2014

   Instructions:
   1. Fill in the following three array values with your MySQL username,
   your MySQL password, and the name of the database you created for
   your WRA 410 project.


   This file defines a global array named $dalConfiguration that you'll
   use when connecting to the database. By putting these settings in a
   PHP "include file", you can set them in just one place, no matter how
   many PHP files need to use them.
   ***/

   $dalConfiguration = array(
      'user'      => 'stevenp5',
      'password'  => 'DrSteveo 1',
      'database'  => 'stevenp5_wra410',
      /***
      You may have to change the hostname setting, depending on your
      web hosting provider.
      ***/
      'hostname'  => 'localhost'
   );
?>

