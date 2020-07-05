<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  $GLOBALS["booknames"] = scandir("books/");
  if (isset($_GET["mode"])) {
    $mode = $_GET["mode"];
    if ($mode === "info") {
      if (isset($_GET["title"])) {
        info($_GET["title"]);
      } else {
        handleError("The title parameter must be set for info mode.");
      }
    } else if ($mode === "description") {
      if (isset($_GET["title"])) {
        description($_GET["title"]);
      } else {
        handleError("The title parameter must be set for description mode.");
      }
    } else if ($mode === "reviews") {
      if (isset($_GET["title"])) {
        reviews($_GET["title"]);
      } else {
        handleError("The title parameter must be set for reviews mode.");
      }
    } else if ($mode === "books") {
      books();
    } else {
      handleError("The value of mode parameter is invalid");
    }
  } else {
    handleError("The mode parameter is required.");
  }

  function handleError($msg) {
    header("HTTP/1.1 400 Invalid Request");
    header("Content-type: text/plain");
    print ("{$msg}\n");
  }

  function info($title) {
    if (isValidTitle($title)) {
      header('Content-Type: application/json');
      $bookInfo = file("./books/" . $title . "/info.txt", FILE_IGNORE_NEW_LINES);
      $jsonInfo = json_encode(array("title" => $bookInfo[0], "author" => $bookInfo[1], "stars" => $bookInfo[2]));
      echo $jsonInfo;
    }
  }

  function description($title) {
    if (isValidTitle($title)) {
      header("Content-type: text/plain");
      $descInfo = file_get_contents("./books/" . $title . "/description.txt");
      echo $descInfo;
    }
  }

  function reviews($title) {
    if (isValidTitle($title)) {
      $output = "";
      $allReviews = glob("./books/" . $title . "/review*.txt");
      foreach ($allReviews as $review) {
        $content = file($review, FILE_IGNORE_NEW_LINES);
        $output = $output . "<h3>" . $content[0] . "<span>" . $content[1] . "</span></h3>\n";
        $output = $output . "<p>" . $content[2] . "</p>\n";
      }
      echo $output;
    }
  }

  function books() {
    header("Content-Type: text/xml");
    $output = "<books>";
    for ($i = 2; $i < count($GLOBALS["booknames"]); $i++) {
      $path = "./books/" . $GLOBALS["booknames"][$i];
      if (!is_dir($path)) {
        continue;
      }
      $title = file("./books/" . $GLOBALS["booknames"][$i] . "/info.txt", FILE_IGNORE_NEW_LINES)[0];
      $output = $output . "<book><title>" . $title . "</title><folder>" . $GLOBALS["booknames"][$i] . "</folder></book>";
    }
    $output = $output . "</books>";
    echo $output;
  }

  function isValidTitle($title) {
    for ($i = 0; $i < count($GLOBALS["booknames"]); $i++) {
      if ($title === $GLOBALS["booknames"][$i]) {
        return true;
      }
    }
    handleError("The title value is invalid");
    return false;
  }
?>