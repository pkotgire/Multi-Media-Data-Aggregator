<?php

  class AddParent {

    function run_query($pDAGR, $cDAGR) {

      $pGUID = $this->get_guid($pDAGR);
      $cGUID = $this->get_guid($cDAGR);

      if ($pGUID != FALSE && $cGUID != FALSE) {
        $conn = open_connection("mmda");
        $query = "INSERT INTO PARENT (GUIDp, GUIDc) VALUES (\"$pGUID\", \"$cGUID\");";
        if ($conn->query($query) === TRUE) {
          echo "Successfully inserted PARENT named \"$pDAGR\" for DAGR \"$cDAGR\" into table \"PARENT\"!<br>";
          close_connection($conn);
          return TRUE;
        } else {
          echo "Error inserting PARENT named \"$pDAGR\" for DAGR \"$cDAGR\" into table \"PARENT\":<br>" . $conn->error . "<br>";
          close_connection($conn);
        }
      }
      return FALSE;
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

  }

?>
