<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  if (isset($_POST["name"]) && isset($_POST["password"])) {
    if (strlen($_POST["name"]) == 0 || strlen($_POST["password"]) == 0) {
      session_destroy();
      session_start();
      $_SESSION["loginfail"] = "missingParam";
      header("Location: start.php");
      exit();
    } else if (userExist($_POST["name"])) {
      if (pwdMatch($_POST["name"], $_POST["password"])) {
        // start a login session
        session_destroy();
        session_start();
        $_SESSION["user"] = $_POST["name"];
        // set cookie
        date_default_timezone_set('America/Los_Angeles');
        $expireTime = time() + 60*60*24*7;
        setcookie("loginTime", date("D y M d, g:i:s a"), $expireTime);
        // redirect to todolist.php
        header("Location: todolist.php");
        exit();
      } else {
        session_destroy();
        session_start();
        $_SESSION["loginfail"] = "mismatch";
        header("Location: start.php");
        exit();
      }
    } else {
      // assume the user wants to create a new account
      if (isValidName($_POST["name"])) {
        if (strlen($_POST["password"]) < 5) {
          session_destroy();
          session_start();
          $_SESSION["loginfail"] = "pwdshort";
          header("Location: start.php");
          exit();
        } else {
          // add to the end of the file
          addNewUser($_POST["name"], $_POST["password"]);
          // start a session
          session_destroy();
          session_start();
          $_SESSION["user"] = $_POST["name"];
          // set cookie
          date_default_timezone_set('America/Los_Angeles');
          $expireTime = time() + 60*60*24*7;
          setcookie("loginTime", date("D y M d, g:i:s a"), $expireTime);
          // redirect to todolist
          header("Location: todolist.php");
          exit();
        }
      } else {
        session_destroy();
        session_start();
        $_SESSION["loginfail"] = "invalidName";
        header("Location: start.php");
        exit();
      }
    }
  } else {
    session_destroy();
    session_start();
    $_SESSION["loginfail"] = "missingParam";
    header("Location: start.php");
    exit();
  }

  function getUserInfo() {
    $info = file("usernames.txt", FILE_IGNORE_NEW_LINES);
    $userInfo = array();
    foreach ($info as $oneUser) {
      $name = explode(":", $oneUser)[0];
      $pwd = explode(":", $oneUser)[1];
      $userInfo[$name] = $pwd;
    }
    return $userInfo;
  }

  function userExist($name) {
    $info = getUserInfo();
    foreach ($info as $username => $pwd) {
      if ($username === $name) {
        return true;
      }
    }
    return false;
  }

  function pwdMatch($name, $pwd) {
    $info = getUserInfo();
    foreach ($info as $username => $password) {
      if ($username === $name) {
        if ($password === $pwd) {
          return true;
        } else {
          return false;
        }
      }
    }
    return false;
  }

  function isValidName($name) {
    $pattern = "/^[a-z]([a-z0-9]){2,7}$/";
    return preg_match($pattern, $name);
  }

  function addNewUser($name, $pwd) {
    $txt = $name . ":" . $pwd . "\n";
    file_put_contents("usernames.txt", $txt, FILE_APPEND);
  }
?>