<?php
  require_once 'db_connection.php';

  class InsertDAGR {

    function run_query($mod_time,	$size, $name, $extension, $file_path, $annotation) {

      // Check to see if DAGR name already exists
      if ($this->dagr_exists($name) == TRUE) {
        echo "The DAGR name \"$name\" already exists! <br>";
        return FALSE;
      }

      // Check to see if file/url already is in the database
      if ($file_path != "" && $this->is_duplicate_file($file_path)){
        echo "The file/URL \"$file_path\" is already in the database, so it will not be re-inserted! <br>";
        return FALSE;
      }

      $GUID = $this->generate_guid();
      $creation_time = time();

      // Construct DAGR query
      $query = "INSERT INTO DAGR (GUID"; // First Part
      $query = ($mod_time != "") ? $query . ", mod_time" : $query;
      $query = ($size != "") ? $query . ", size" : $query;
      $query .= ", name";
      $query = ($extension != "") ? $query . ", extension" : $query;
      $query = ($file_path != "") ? $query . ", file_path" : $query;
      $query = ($annotation != "") ? $query . ", annotation" : $query;
      $query .= ", creation_time";
      $query .= ") VALUES (\"$GUID\"";  // Second Part
      $query = ($mod_time != "") ? $query . ", \"$mod_time\"" : $query;
      $query = ($size != "") ? $query . ", \"$size\"" : $query;
      $query .= ", \"$name\"";
      $query = ($extension != "") ? $query . ", \"$extension\"" : $query;
      $query = ($file_path != "") ? $query . ", \"$file_path\"" : $query;
      $query = ($annotation != "") ? $query . ", \"$annotation\"" : $query;
      $query .= ", \"$creation_time\"";
      $query .= ");";

      // Run dagr query and return false if failed
      $conn = open_connection("mmda");
      if ($conn->query($query) === FALSE) {
        close_connection($conn);
        return FALSE;
      }

      // Return true if Successfully
      return TRUE;

    }

    function generate_guid(){
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double)microtime()*10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
            return $uuid;
        }
    }

    // Checks to see if the given dagr name alredy exists
    function dagr_exists($DAGR) {
      $query = "SELECT NAME, GUID FROM DAGR WHERE NAME = \"$DAGR\";";
      $conn = open_connection("mmda");
      $result = mysqli_query($conn, $query);
      close_connection($conn);
      if (mysqli_num_rows($result) > 0) {
        return TRUE;
      } else {
        return FALSE;
      }
    }

    // Checks to see if the given file is already in the database
    function is_duplicate_file($file_path) {
      $query = "SELECT GUID, FILE_PATH FROM DAGR WHERE FILE_PATH = \"$file_path\";";
      $conn = open_connection("mmda");
      $result = mysqli_query($conn, $query);
      close_connection($conn);
      if (mysqli_num_rows($result) > 0) {
          return TRUE;
      } else {
          return FALSE;
      }
    }

    function get_guid($DAGR) {
      $query = "SELECT GUID FROM DAGR WHERE NAME = \"$DAGR\";";
      $conn = open_connection("mmda");
      $result = mysqli_query($conn, $query);
      close_connection($conn);
      if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result)["GUID"];
      } else {
          return FALSE;
      }
    }

    function get_dagr($file_path) {
      $query = "SELECT NAME FROM DAGR WHERE FILE_PATH = \"$file_path\";";
      $conn = open_connection("mmda");
      $result = mysqli_query($conn, $query);
      close_connection($conn);
      if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result)["NAME"];
      } else {
          return FALSE;
      }
    }
  }

?>
