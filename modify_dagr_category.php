<?php
  require_once 'db_connection.php';

  class ModifyDAGRCategory{

  	function run_query($DAGRname, $category){
  		$conn = open_connection("mmda");
  		$result = $conn->query($this->get_GUID_query($DAGRname));


  		if($result->num_rows > 0){
  			$row = $result->fetch_assoc();
  			$GUID = $row["GUID"];

  			$catResult = $conn->query($this->get_category_exists_query($category));

  			if($catResult->num_rows === 0 && $conn->query($this->get_insert_category_query($category)) === FALSE){
  				echo "Your category '$category' could not be added!<br>";
  				return;
  			}

  			$gCatResult = $conn->query($this->get_GUID_has_category_query($GUID));

  			if($gCatResult->num_rows > 0 && $conn->query($this->get_remove_GUID_category_query($GUID)) === FALSE){
  				echo "Your DAGR '$DAGRname's current category could not be deleted!";
  				return;
  			}

  			if($conn->query($this->get_give_GUID_category_query($GUID, $category)) === TRUE){
  				echo "The DAGR '$DAGRname' was successfully added to the category '$category'!<br>";
  			} else{
  				echo "There was an issue adding '$DAGRname' to '$category'.<br>";
  			}

  		} else{
  			echo "The DAGR $DAGRname you entered does not exist!<br>";
  		}

  		close_connection($conn);
  	}

  	function get_GUID_query($DAGRn){
      	return "SELECT d.GUID as GUID FROM DAGR d WHERE d.name=\"$DAGRn\";";
    }

    function get_category_exists_query($category){
    	return "SELECT * FROM sub_category s WHERE s.c_nameS = '$category'";
    }

    function get_GUID_has_category_query($GUID){
    	return "SELECT * FROM category c WHERE c.GUID = '$GUID'";
    }

    function get_insert_category_query($category){
    	return "INSERT INTO sub_category(c_nameS) VALUES('$category')";
    }

    function get_remove_GUID_category_query($GUID){
    	return "DELETE FROM category WHERE GUID = '$GUID'";
    }

    function get_give_GUID_category_query($GUID, $cat){
    	return "INSERT INTO category VALUES('$GUID','$cat')";
    }
  }

 ?>
