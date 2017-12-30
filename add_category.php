<?php
  require_once 'db_connection.php';

  class AddCategory{

  	function run_query($category){
  		$conn = open_connection("mmda");

  		$catResult = $conn->query($this->get_category_exists_query($category));

      if($catResult->num_rows > 0){
        echo "Your category '$category' already exists!";
      } else if($conn->query($this->get_insert_category_query($category)) === TRUE){
        echo "Your category '$category' has been inserted successfully!";
      } else{
        echo "Your category '$category' could not be added.";
      }

  		close_connection($conn);
  	}

    function get_category_exists_query($category){
      return "SELECT * FROM sub_category s WHERE s.c_nameS = '$category'";
    }

    function get_insert_category_query($category){
      return "INSERT INTO sub_category(c_nameS) VALUES('$category')";
    }
  }

 ?>