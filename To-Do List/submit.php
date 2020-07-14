<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  session_start();
  if (isset($_POST["action"])) {
    if ($_POST["action"] == "add") {
      if (isset($_POST["item"])) {
        if (strlen($_POST["item"]) > 0) { // only add to list if event is non empty
          addItem($_POST["item"]);
        }
      } else {
        echo "Item parameter must be present with action.";
        die();
      }
    } else if ($_POST["action"] == "delete") {
      if (isset($_POST["index"])) {
        deleteItem($_POST["index"]);
      } else {
        echo "Index parameter must be present with action.";
        die();
      }
    } else {
      echo "The action parameter must be add or delete.";
      die();
    }
  } else {
    echo "Missing the action parameter.";
    die();
  }

  function addItem($newItem) {
    $user = $_SESSION["user"];
    $path = "./todolist/todo_" . $user . ".txt";
    $input = $newItem . "\n";
    $add = fopen($path, 'a');
    fwrite($add, $input);
  }

  function deleteItem($index) {
    $user = $_SESSION["user"];
    $path = "./todolist/todo_" . $user . ".txt";
    $events = file($path, FILE_IGNORE_NEW_LINES);
    if (!is_numeric($index) || $index >= count($events)) {
      echo "The index parameter is wrong.";
      die();
    } else {
      unset($events[$index]);
      file_put_contents($path, "");
      $add = fopen($path, 'a');
      foreach ($events as $event) {
        fwrite($add, $event . "\n");
      }
    }
  }

  header("Location: todolist.php");
  exit();
?>