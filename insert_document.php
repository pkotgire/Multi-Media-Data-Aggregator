<?php

  require_once 'db_connection.php';
  require_once 'insert_dagr.php';
  require_once 'add_parent.php';
  require_once 'modify_dagr_category.php';

  class InsertDocument {

    function run_query($file_path, $DAGR, $pDAGR, $category, $annotation, $parse) {

      // Check to see if file exists
      if (!file_exists($file_path)){
        echo "The file \"$file_path\" does not exist, so a DAGR will not be created!<br>";
        return FALSE;
      }

      // Create variables needed for dagr query
      $DAGR = ($DAGR != "") ? $DAGR : $file_path;
      $mod_time = $size = $extension = "";

      $mod_time = filemtime($file_path);
      $size = filesize($file_path);
      $extension = pathinfo($file_path, PATHINFO_EXTENSION);

      // Attempt to insert dagr
      $InsertDAGR = new InsertDAGR();
      $result = $InsertDAGR->run_query($mod_time, $size, $DAGR, $extension, $file_path, $annotation);

      if ($result === FALSE) {
        if ($InsertDAGR->is_duplicate_file($file_path) && $pDAGR != ""){
          echo "The file '$file_path' already exists in the database, but the parent '$pDAGR' will still be attached...<br>";
          $this->attach_parent($pDAGR, $InsertDAGR->get_dagr($file_path));
        } else {
          echo "Error inserting DAGR '$DAGR' for file '$file_path'! <br>";
        }
        return FALSE;
      } else {
        echo "DAGR '$DAGR' was successfully created for file '$file_path'! <br>";
      }

      // Add category to DAGR if not empty string
      if ($category != "") {
        $ModifyDAGRCategory = new ModifyDAGRCategory();
        $ModifyDAGRCategory->run_query($DAGR, $category);
      }

      // Add Parent if field not empty
      $this->attach_parent($pDAGR, $DAGR);

      // Parse file if it is html
      if ($parse === TRUE && $extension == "html") {
        $ParseHTML = new ParseHTML();
        $ParseHTML->run_query($file_path, $DAGR, 2, TRUE);
      }

      return TRUE;
    }

    function attach_parent($pDAGR, $cDAGR) {
      if ($pDAGR != "") {
        // Create Parent DAGR if it doesnt exist
        $InsertDAGR = new InsertDAGR();
        if ($InsertDAGR->dagr_exists($pDAGR) == FALSE) {
          echo "DAGR named \"$pDAGR\" does not exist, creating it...<br>";
          $result = $InsertDAGR->run_query("", "", $pDAGR, "", "", "");

          if ($result === TRUE) {
            echo "Successfully inserted parent DAGR named \"$pDAGR\" with no file into table \"DAGR\"!<br>";
          } else {
            echo "Error inserting parent DAGR named \"$pDAGR\" into table \"DAGR\"!<br>";
          }
        }
        // Attach parent to DAGR
        $AddParent = new AddParent();
        $AddParent->run_query($pDAGR, $cDAGR);
      }
    }
  }

?>
