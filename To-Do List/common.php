<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  function head() {
    $output = "<head><meta charset='utf-8' /><title>Remember the Cow</title>
		<link href='https://webster.cs.washington.edu/css/cow-provided.css' type='text/css' rel='stylesheet' />
		<link href='cow.css' type='text/css' rel='stylesheet' />
    <link href='favicon.ico' type='image/ico' rel='shortcut icon' /></head>";
    echo $output;
  }

  function banner() {
    $output = "<div class='headfoot'><h1><img src='logo.gif' alt='logo' />
    Remember<br />the Cow</h1></div>";
    echo $output;
  }

  function footer() {
    $output = "<div class='headfoot'><p>
    &quot;Remember The Cow is nice, but it's a total copy of another site.&quot; - PCWorld<br />
    All pages and content &copy; Copyright CowPie Inc.</p>
    <div id='w3c'><a href='https://webster.cs.washington.edu/validate-html.php'>
    <img src='w3c-html5.png' alt='Valid HTML' /></a>
    <a href='https://webster.cs.washington.edu/validate-css.php'>
    <img src='w3c-css.png' alt='Valid CSS' /></a></div></div>";
    echo $output;
  }

  function title() {
    $output = "<h2>" . $_SESSION["user"] . "'s To-Do List</h2>";
    echo $output;
  }

  function logout() {
    if (isset($_COOKIE["loginTime"])) {
      $time = str_replace("+", " ", $_COOKIE["loginTime"]);
      $output = "<div><a href='logout.php'><strong>Log Out</strong></a>
      <em>(logged in since " . $time . ")</em></div>";
      echo $output;
    }
  }

  function lastLogin() {
    if (isset($_COOKIE["loginTime"])) {
      $time = str_replace("+", " ", $_COOKIE["loginTime"]);
      $output = "<p><em>(last login from this computer was " . $time . ")</em></p>";
      echo $output;
    }
  }

  function loginForm() {
    $output = "<form id='loginform' action='login.php' method='post'>
    <div><input name='name' type='text' size='8' autofocus='autofocus' /> <strong>User Name</strong></div>
    <div><input name='password' type='password' size='8' /> <strong>Password</strong></div>
    <div><input type='submit' value='Log in' class='btn' /></div></form>";
    echo $output;
  }

  function generateToDoList() {
    $output = "<ul id='todolist'>";
    $added = scandir("./todolist/");
    $target = "todo_" . $_SESSION["user"] . ".txt";
    $exist = false;
    foreach ($added as $file) {
      if ($file == $target) {
        $exist = true;
      break;
      }
    }
    if ($exist == true) {
      $events = file("./todolist/" . $target, FILE_IGNORE_NEW_LINES);
      $index = 0;
      foreach ($events as $event) {
        $output .= "<li>
        <form action='submit.php' method='post'>" . $event . "
          <input type='hidden' name='action' value='delete' />
          <input type='hidden' name='index' value=" . $index . " />
          <input type='submit' value='Delete' class='btn' style='float: right;'/>
        </form></li>";
        $index += 1;
      }
    }
    $output .= "<li>
    <form action='submit.php' method='post'>
      <input type='hidden' name='action' value='add' />
      <input name='item' type='text' size='25' autofocus='autofocus' />
      <input type='submit' value='Add' class='btn' style='float: right;'/>
    </form></li></ul>";
    echo $output;
  }

  function frontPageSetUp() {
    $output = "<p>The best way to manage your tasks. <br />
    Never forget the cow (or anything else) again!</p>
    <p>Log in now to manage your to-do list. <br />
    If you do not have an account, one will be created for you.
    </p>";
  echo $output;
  }

  function handle_error() {
    if (isset($_SESSION["loginfail"])) {
      if ($_SESSION["loginfail"] == "mismatch") {
        $output = "<p id='loginfail'>Incorrect password.</p>";
        echo $output;
      } else if ($_SESSION["loginfail"] == "invalidName") {
        $output = "<p id='loginfail'>Trying to create a new account but username is invalid.</p>";
        echo $output;
      } else if ($_SESSION["loginfail"] == "missingParam") {
        $output = "<p id='loginfail'>Username or password is missing.</p>";
        echo $output;
      } else if ($_SESSION["loginfail"] == "pwdshort") {
        $output = "<p id='loginfail'>Trying to create a new account but password is too short. Password must be longer than 5 characters.</p>";
        echo $output;
      }
    }
  }
?>