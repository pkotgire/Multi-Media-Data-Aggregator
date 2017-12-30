<?php

  class ParseHTML {

    function run_query($url, $pDAGR, $depth, $is_local) {

      // Check depth
      if ($depth <= 0) {
        return;
      }

      // Only parse local file if it exists
      if ($is_local && !file_exists($url)) {
        echo "The file \"$url\" does not exist, so it will not be parsed! <br>";
        return;
      }

      // Only parse local file if it exists
      if ($is_local && !file_exists($url)) {
        echo "The file \"$url\" does not exist, so it will not be parsed! <br>";
        return;
      }

      // Only parse url if it exists
      if (!$is_local && !$this->url_exists($url)) {
        // echo "The url \"$url\" does not exist, so it will not be parsed! <br>";
        return;
      }

      $InsertDocument = new InsertDocument();
      $InsertURL = new InsertURL();
      $InsertDAGR = new InsertDAGR();
      $components = $this->get_links($url, $is_local);

      if ($is_local) {
        foreach ($components as $component){
          if (substr($component, 0, 4 ) === "http"){
            $InsertURL->run_query($component, "", $pDAGR, "", "", FALSE);
            $this->run_query($component, $InsertDAGR->get_dagr($component), $depth - 1, FALSE);
          } else {
            $InsertDocument->run_query($component, "", $pDAGR, "", "", FALSE);
            $this->run_query($component, $InsertDAGR->get_dagr($component), $depth - 1, TRUE);
          }
        }
      }
      else {
        foreach ($components as $component){
            $new_component = $component;
            if (substr($component, 0, 4 ) != "http") {
              if (substr($component, 0, 2 ) === "//"){
                $new_component = "https:" . $component;
              }
              else if (substr($component, 0, 1 ) === "/" && substr($url, -1) != "/") {
                $new_component = $url . $component;
              } else if (substr($component, 0, 1 ) === "/" && substr($url, -1) == "/"){
                $new_component = substr($url, 0, -1) . $component;
              } else if (substr($component, 0, 1 ) != "/" && substr($url, -1) != "/") {
                $new_component = $url . "/" . $component;
              } else if (substr($component, 0, 1 ) != "/" && substr($url, -1) == "/"){
                $new_component = $url . $component;
              }
            }
            $InsertURL->run_query($new_component, "", $pDAGR, "", "", FALSE);
            $this->run_query($new_component, $InsertDAGR->get_dagr($new_component), $depth - 1, FALSE);
        }
      }

    }

    function get_links($url, $is_local) {

      $html = file_get_contents($url);
      $dom = new DOMDocument;
      @$dom->loadHTML($html);

      //Get all links. You could also use any other tag name here,
      //like 'img' or 'table', to extract other tags.
      $links[0] = $dom->getElementsByTagName('a');
      $links[1] = $dom->getElementsByTagName('link');
      $links[2] = $dom->getElementsByTagName('img');

      $filtered = array();
      //Iterate over the extracted links and display their URLs

      if (!empty($links[0]) && !empty($links[1]) && !empty($links[2])) {
        echo "<br>Now parsing \"" . $url . "\"!<br>";
      }

      // echo "a href links <br>";
      foreach ($links[0] as $link){
        // echo $link->nodeValue;
        // echo $link->getAttribute('href'), '<br>';
        $path = $link->getAttribute('href');
        if ($is_local && substr($path, 0, 4 ) != "http") {
          $path = pathinfo($url, PATHINFO_DIRNAME) . "/" . $path;
        }
        $path = str_replace(' ', '', $path);
        if ($path != $url && $path != "#" && $path != "" && $path != "/") {
            // echo $path . "<br>";
            array_push($filtered, $path);
        }
      }

      // echo "<br>link href links <br>";
      foreach ($links[1] as $link){
        $path = $link->getAttribute('href');
        if ($is_local) {
          $path = pathinfo($url, PATHINFO_DIRNAME) . "/" . $path;
        }
        $path = str_replace(' ', '', $path);
        if ($path != $url && $path != "#" && $path != "" && $path != "/") {
            // echo $path . "<br>";
            array_push($filtered, $path);
        }
      }

      // echo "<br>img src links <br>";
      foreach ($links[2] as $link){
        $path = $link->getAttribute('src');
        if ($is_local) {
          $path = pathinfo($url, PATHINFO_DIRNAME) . "/" . $path;
        }
        $path = str_replace(' ', '', $path);
        if ($path != $url && $path != "#" && $path != "" && $path != "/") {
          // echo $path . "<br>";
          array_push($filtered, $path);
        }
      }

      $filtered = array_unique($filtered);

      return $filtered;
    }

    function url_exists($url){
      $handle = curl_init($url);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        curl_close($handle);

        if($httpCode >= 200 && $httpCode <= 400) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
  }
?>
