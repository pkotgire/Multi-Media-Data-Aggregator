<?php

  require_once 'db_connection.php';

  class BulkDataEntry {

    function run_query($dirpath, $parentDAGR, $category, $annotation){

      // Check to see if directory exists
      if (!file_exists($dirpath) || !is_dir($dirpath)){
        echo "That directory does not exist! <br>";
        return;
      }

      // Create an array of file paths in that directory
      if (substr($dirpath, -1) != "/")
        $dirpath = $dirpath . "/";
      $file_paths = glob($dirpath . "*");

      // Make sure parent DAGR has a value
      $parentDAGR = ($parentDAGR != "") ? $parentDAGR : $dirpath;

      $InsertDAGR = new InsertDAGR();
      $dirExists = $InsertDAGR->is_duplicate_file($dirpath);

      if ($dirExists) {
        $parentDAGR = $InsertDAGR->get_dagr($dirpath);
      } else {
        echo "A DAGR for the directory '$dirpath' does not exist, creating one...<br>";
        $InsertDocument = new InsertDocument();
        $result = $InsertDocument->run_query($dirpath, $parentDAGR, "", $category, $annotation, FALSE);
        if ($result == FALSE) {
          echo "There was an error creating a DAGR for the directory '$dirpath'; will not bulk insert...";
          return;
        }
        echo "<br>";
      }

      // Try to insert each of these files in the mmda
      $InsertDocument = new InsertDocument();
      for ($i = 0; $i < sizeof($file_paths); $i++) {
        if (is_dir($file_paths[$i]))
          $file_paths[$i] = $file_paths[$i] . "/";
        echo "Attempting to insert \"" . $file_paths[$i] . "\"...<br>";
        $InsertDocument->run_query($file_paths[$i], "",
          $parentDAGR, $category, $annotation, FALSE);
        if (is_dir($file_paths[$i])) {
          echo "<br>";
          $this->run_query($file_paths[$i], "", $category, $annotation);
        }
        echo "<br>";
      }
    }

  }

?>
