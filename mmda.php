<!DOCTYPE html>
<html>
<head>
<style>
* {box-sizing: border-box}
body {font-family: "Lato", sans-serif;}

/* Style the tab */
div.tab {
    float: left;
    /*border: 1px solid #ccc;*/
    background-color: #f1f1f1;
    width: 20%;
    height: 300px;
}

/* Style the buttons inside the tab */
div.tab button {
    display: block;
    background-color: inherit;
    color: black;
    padding: 22px 16px;
    width: 100%;
    border: none;
    outline: none;
    text-align: left;
    cursor: pointer;
    transition: 0.3s;
    font-size: 21px;
}

/* Change background color of buttons on hover */
div.tab button:hover {
    background-color: #ddd;
}

/* Create an active/current "tab button" class */
div.tab button.active {
    background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
    float: left;
    padding: 0px 12px;
    /*border: 1px solid #ccc;*/
    width: 80%;
    border-left: none;
    height: 300px;
}
</style>
</head>
<body>

<!-- Create different tabs -->
<div class="tab">
  <button class="tablinks" onclick="openChoice(event, 'output')" id="defaultOpen">Output</button>
  <button class="tablinks" onclick="openChoice(event, 'insert')">Insert DAGR</button>
  <button class="tablinks" onclick="openChoice(event, 'delete')">Delete DAGR</button>
  <button class="tablinks" onclick="openChoice(event, 'cc')">Categories</button>
  <button class="tablinks" onclick="openChoice(event, 'rq')">Reach Query</button>
  <button class="tablinks" onclick="openChoice(event, 'gm')">Get Metadata</button>
  <button class="tablinks" onclick="openChoice(event, 'osr')">Orphan and Sterile Report</button>
  <button class="tablinks" onclick="openChoice(event, 'trr')">Time-Range Report</button>
</div>

<!--Output Tab-->
<div id="output" class="tabcontent">
  <form action="" method="post">
    <fieldset>
      <legend>Output:</legend>
    <output>
    <?php
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once 'insert_document.php';
        require_once 'bulk_data_entry.php';
        require_once 'insert_url.php';
        require_once 'delete_dagr.php';
        require_once 'parse_html.php';
        require_once 'add_parent.php';
        require_once 'insert_dagr.php';

        $task = $_POST["action"];

        if ($task == 'ID') {
          $InsertDocument = new InsertDocument();
          $InsertDocument->run_query($_POST["filepath"], $_POST["DAGR"],
            $_POST["parentDAGR"], $_POST["category"], $_POST["annotation"], TRUE);
        }
        else if ($task == 'BDE') {
          $BulkDataEntry = new BulkDataEntry();
          $BulkDataEntry->run_query($_POST["dirpath"], $_POST["parentDAGR"],
          $_POST["category"], $_POST["annotation"]);
        }
        else if ($task == 'URL') {
          $InsertURL = new InsertURL();
          $InsertURL->run_query($_POST["url"], $_POST["DAGR"],
            $_POST["parentDAGR"], $_POST["category"], $_POST["annotation"], TRUE);
        }
        else if ($task == 'DD') {
          /*$DeleteDAGR = new DeleteDAGR();
          $DeleteDAGR->run_query($_POST["dagrname"]);*/
          echo "<br><br> <form action=\"\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"DPC\">";
          echo "<br><input type=\"submit\" value=\"Submit\"></form>";
        }
        else if ($task == 'DPC') {
          echo "It worked!";
        }
        else if ($task == 'CC') {
          $ParseHTML = new ParseHTML();
          $ParseHTML->get_links("http://apo-em.org/images/u1703-4.png?crc=3758827547");
        }
      }
     ?>
     <br>
    </output>
  </fieldset>
  </form>
</div>

<!--Insert Tab-->
<div id="insert" class="tabcontent">

  <!-- Insert Document -->
  <form action=""  method="post">
	  <fieldset>
      <input type="hidden" name="action" value="ID">
	    <legend>New Document:</legend>
	    File Path:
	    <input type="text" name="filepath"><br><br>
      DAGR Name:
	    <input type="text" name="DAGR"> &nbsp;
      Parent DAGR Name:
	    <input type="text" name="parentDAGR"><br><br>
	    Category:
	    <input type="text" name="category"> &nbsp;
      Annotation:
	    <input type="text" name="annotation"><br><br>
	    <input type="submit" value="Submit">
	  </fieldset>
  </form>
  <br>

  <!-- Bulk Data Entry -->
  <form action=""  method="post">
	  <fieldset>
      <input type="hidden" name="action" value="BDE">
	    <legend>Bulk Data Entry:</legend>
      Directory Path:
	    <input type="text" name="dirpath"><br><br>
      Parent DAGR Name:
	    <input type="text" name="parentDAGR"><br><br>
	    Category:
	    <input type="text" name="category"><br><br>
      Annotation:
	    <input type="text" name="annotation"><br><br>
	    <input type="submit" value="Submit">
	  </fieldset>
  </form>
  <br>

  <!-- Insert URL -->
  <form action=""  method="post">
	  <fieldset>
      <input type="hidden" name="action" value="URL">
	    <legend>Insert URL:</legend>
	    URL:
	    <input type="text" name="url"><br><br>
      DAGR Name:
	    <input type="text" name="DAGR"> &nbsp;
      Parent DAGR Name:
	    <input type="text" name="parentDAGR"><br><br>
	    Category:
	    <input type="text" name="category"> &nbsp;
      Annotation:
	    <input type="text" name="annotation"><br><br>
	    <input type="submit" value="Submit">
	  </fieldset>
  </form>
</div>

<!-- Delete DAGR -->
<div id="delete" class="tabcontent">
  <form action=""  method="post">
  <fieldset>
      <input type="hidden" name="action" value="DD">
      Enter DAGR name of the DAGR you'd like to delete from the database:<br><br>
      DAGR Name:<br>
      <input type="text" name="dagrname"><br><br>
      <input type="submit" value="Submit">
      </fieldset>
  </form>
</div>

<!-- Categories -->
<div id="cc" class="tabcontent">

<!--Modify File Category-->
  <form action=""  method="post">
    <fieldset>
      <input type="hidden" name="action" value="MFC">
      <legend>Modify File Category:</legend>
      Enter GUID, File Path or both of the DAGR you'd like to edit the category of:<br><br>
      GUID:<br>
      <input type="text" name="guid"><br>
      <br>
      <input type="hidden" name="action" value="CC">
      File Path:<br>
      <input type="text" name="filepath"><br>
      <br>
      Category:<br>
      <input type="text" name="category"><br><br>
      <input type="submit" value="Submit">
      </fieldset>
  </form>
  <br>

  <!-- Add Category -->
  <form action=""  method="post">
    <fieldset>
      <input type="hidden" name="action" value="AC">
      <legend>Add Category:</legend>
      Enter category you'd like to add:<br><br>
      Category:<br>
      <input type="text" name="category"><br><br>
      <input type="submit" value="Submit">
    </fieldset>
  </form>
  <br>

  <!-- Add Subcategory to Category -->
  <form action=""  method="post">
    <fieldset>
      <input type="hidden" name="action" value="ASC">
      <legend>Add Subcategory to Category:</legend>
      Enter subcategory you'd like to add to the specified category:<br><br>
      Subcategory:<br>
      <input type="text" name="subcategory"><br>
      <br>
      Category:<br>
      <input type="text" name="category"><br><br>
      <input type="submit" value="Submit">
    </fieldset>
  </form>
</div>

<!-- Reach Query -->
<div id="rq" class="tabcontent">
  <form action= ""  method="post">
  <fieldset>
      <input type="hidden" name="action" value="RQ">
      Enter GUID, File Path or both of the DAGR you'd like to perform a Reach Query for:<br><br>
      GUID:<br>
      <input type="text" name="guid"><br>
      <br>
      File Path:<br>
      <input type="text" name="filepath"><br><br>
      <input type="submit" value="Submit">
      </fieldset>
  </form>
</div>

<!-- Get Metadata -->
<div id="gm" class="tabcontent">
  <form action= ""  method="post">
  <fieldset>
      <input type="hidden" name="action" value="GM">
      Enter GUID, File Path or both of the DAGR you'd like to view:<br><br>
      GUID:<br>
      <input type="text" name="guid"><br>
      <br>
      File Path:<br>
      <input type="text" name="filepath"><br><br>
      <input type="submit" value="Submit">
      </fieldset>
  </form>
</div>

<!-- Orphan and Sterile Report -->
<div id="osr" class="tabcontent">
  <form action= ""  method="post">
  <fieldset>
      <input type="hidden" name="action" value="OSR">
      Click Submit for Orphan and Sterile Report of the database: <br><br>
      <input type="submit" value="Submit">
      </fieldset>
  </form>
</div>

<!-- Time Range Report -->
<div id="trr" class="tabcontent">
  <form action= ""  method="post">
  <fieldset>
      <input type="hidden" name="action" value="TRR">
      Enter start and end time in the following format MM/DD/YYYY HH:mm:ss (military time):<br><br>
      Start Time:<br>
      <input type="text" name="guid"><br>
      <br>
      End Time:<br>
      <input type="text" name="filepath"><br><br>
      <input type="submit" value="Submit">
      </fieldset>
  </form>
</div>

<!-- JavaScript code to display appropriate tab -->
<script>
function openChoice(evt, choice) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(choice).style.display = "block";
    evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>

</body>
</html>
