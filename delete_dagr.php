<?php
  require_once 'db_connection.php';

  class DeleteDAGR {

    function run_query($DAGRname) {
      $conn = open_connection("mmda");
      $query = $this->get_GUID_query($DAGRname);

      $result = $conn->query($query);
      if ($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $currGUID = $row["GUID"];

        $pResults = $conn->query($this->get_parents($currGUID));
        $cResults = $conn->query($this->get_children($currGUID));

        $echo_str = "";

        if($conn->query($this->get_delete_from_parent($currGUID)) === TRUE &&
          $conn->query($this->get_delete_from_category($currGUID)) === TRUE &&
          $conn->query($this->get_delete_from_DAGR($currGUID)) === TRUE){
          $echo_str .= "'$DAGRname' was sucessfully deleted!";
        }

        //two files deleteparentandchild and deleteDAGR
        //

        /*<input type="radio" name="choice" value="ade" checked> Automatic Data Entry<br>*/
        if($pResults->num_rows > 0 || $cResults->num_rows > 0){
          $var = $_SERVER["PHP_SELF"];
          $action = "action=\"<?php echo htmlspecialchars($var);?>\"";
          $echo_str .= "<br><br> <form action=\"\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"DPC\">";
        }

        if($pResults->num_rows > 0){
          $echo_str .= "<fieldset><legend>Parents:</legend>";
          $echo_str .= "Select box of parents you would like to delete:<br>";
          //loop through parents and decide whether or not to delete
          //$echo_str .= "<input type=\"checkbox\" value=\"p\"> Parent<br>";
          while($currP = $pResults->fetch_assoc()){
            $GUID = $currP["parent"];
            $n_result = $conn->query($this->get_DAGRname_query($GUID));
            $name = $n_result->fetch_assoc();
            $echo_str .= "<input type=\"checkbox\" value=\"$GUID\"> ".$name["name"]." ($GUID)<br>";
          }
          $echo_str .= "</fieldset>";
        }

        if($cResults->num_rows > 0){
           $echo_str .= "<br><fieldset><legend>Children: </legend>Select boxes for DAGRs you'd like to deep delete. By default a shallow delete will be done:<br>";
           //$echo_str .= "<input type=\"checkbox\" value=\"p\"> Child<br>";
          //loop through children and decide to shallow or deep delete
          while($currC = $cResults->fetch_assoc()){
            $GUID = $currC["child"];
            $n_result = $conn->query($this->get_DAGRname_query($GUID));
            $name = $n_result->fetch_assoc();
            $echo_str .= "<input type=\"checkbox\" value=\"$GUID\"> ".$name["name"]." ($GUID)<br>";
          //if shallow
          }

          $echo_str .= "</fieldset>";
        }

        if($pResults->num_rows > 0 || $cResults->num_rows > 0){
          $echo_str .= "<br><input type=\"submit\" value=\"Submit\"></form>";
        }



        close_connection($conn);
        echo $echo_str;

      } else{
        echo "The DAGR name you entered doesn't exist.";
        close_connection($conn);
      }


    }

    function get_GUID_query($DAGRn){
      return "SELECT d.GUID as GUID FROM DAGR d WHERE d.name=\"$DAGRn\";";
    }

    function get_DAGRname_query($GUID){
      return "SELECT d.name as name FROM DAGR d WHERE d.GUID=\"$GUID\";";
    }

    function get_parents($GUID){
      return "SELECT p.GUIDp as parent FROM Parent p, DAGR d WHERE d.GUID = '$GUID' and d.GUID = p.GUIDc;";
    }

    function get_children($GUID){
      return "SELECT p.GUIDc as child FROM Parent p, DAGR d WHERE d.GUID = '$GUID' and d.GUID = p.GUIDp;";
    }

    function get_delete_from_DAGR($GUID){
      return "DELETE FROM DAGR WHERE GUID = '$GUID';";
    }

    function get_delete_from_parent($GUID){
      return "DELETE FROM Parent WHERE GUIDp = '$GUID' or GUIDc = '$GUID';";
    }

    function get_delete_from_category($GUID){
      return "DELETE FROM Category WHERE GUID = '$GUID';";
    }
  }

?>
