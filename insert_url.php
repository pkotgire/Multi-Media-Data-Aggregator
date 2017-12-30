<?php

  require_once 'db_connection.php';
  require_once 'insert_dagr.php';
  require_once 'modify_dagr_category.php';

  class InsertURL {

    function run_query($url, $DAGR, $pDAGR, $category, $annotation, $parse) {

      // Get values needed to insert url into dagr table and create query
      $DAGR = ($DAGR != "") ? $DAGR : $url;

      // Run dagr query and check to see if insert url into dagr table failed
      $InsertDAGR = new InsertDAGR();
      $result = $InsertDAGR->run_query("", "", $DAGR, "", $url, $annotation);

      // Only continue if DAGR was created for URL
      if ($result === FALSE) {
        if ($InsertDAGR->is_duplicate_file($url) && $pDAGR != ""){
          echo "The URL '$url' already exists in the database, but the parent '$pDAGR' will still be attached...<br>";
          $this->attach_parent($pDAGR, $InsertDAGR->get_dagr($url));
        } else {
          echo "Error inserting DAGR '$DAGR' for URL '$url'! <br>";
        }
        return FALSE;
      } else {
        echo "DAGR '$DAGR' was successfully created for URL '$url'! <br>";
      }

      // Add category to DAGR if not empty string
      if ($category != "") {
        $ModifyDAGRCategory = new ModifyDAGRCategory();
        $ModifyDAGRCategory->run_query($DAGR, $category);
      }

      // Add Parent if field not empty
      $this->attach_parent($pDAGR, $DAGR);

      // Get parameters for URL
      $url_parameters = parse_url($url);
      $scheme = array_key_exists('scheme', $url_parameters) ? $url_parameters['scheme'] : FALSE;
      $host_name = array_key_exists('host', $url_parameters) ? $url_parameters['host'] : FALSE;
      $port = array_key_exists('post', $url_parameters) ? $url_parameters['post'] : FALSE;
      $path = array_key_exists('path', $url_parameters) ? $url_parameters['path'] : FALSE;
      $query_field = array_key_exists('query', $url_parameters) ? $url_parameters['query'] : FALSE;

      // Get the GUID of the newly created DAGR
      $GUID = $InsertDAGR->get_guid($DAGR);

      // Construct URL_DAGR query
      $query = "INSERT INTO URL_DAGR (GUID"; // First Part
      $query = ($scheme != FALSE) ? $query . ", scheme" : $query;
      $query = ($host_name != FALSE) ? $query . ", host_name" : $query;
      $query = ($port != FALSE) ? $query . ", port" : $query;
      $query = ($path != FALSE) ? $query . ", path" : $query;
      $query = ($query_field != FALSE) ? $query . ", query_field" : $query;
      $query .= ") VALUES (\"$GUID\"";  // Second Part
      $query = ($scheme != FALSE) ? $query . ", \"$scheme\"" : $query;
      $query = ($host_name != FALSE) ? $query . ", \"$host_name\"" : $query;
      $query = ($port != FALSE) ? $query . ", \"$port\"" : $query;
      $query = ($path != FALSE) ? $query . ", \"$path\"" : $query;
      $query = ($query_field != FALSE) ? $query . ", \"$query_field\"" : $query;
      $query .= ");";

      // Run url_dagr query and check to see if insert url into url_dagr table failed
      $conn = open_connection("mmda");
      if ($conn->query($query) === FALSE) {
        echo "Error inserting URL_DAGR for url \"$url\" into table \"URL_DAGR\":<br>" . $conn->error . "<br>";
        close_connection($conn);
        return;
      }

      // Notify user that insertion of url into url_dagr table was succesful
      echo "Successfully inserted URL_DAGR for url \"$url\" into table \"URL_DAGR\"!<br>";

      // Parse the url for its components
      if ($parse === TRUE) {
        $ParseHTML = new ParseHTML();
        $ParseHTML->run_query($url, $DAGR, 2, FALSE);
      }

    }

    function valid_url($url) {
      return filter_var($url, FILTER_VALIDATE_URL) !== false;
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
