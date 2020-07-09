<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  function get_PDO() {
    $host =  "localhost";
    $port = "8889";
    $user = "root";
    $password = "root";
    $dbname = "kevin";

    $ds = "mysql:host={$host}:{$port};dbname={$dbname};charset=utf8";

    try {
      $db = new PDO($ds, $user, $password);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $db;
    } catch (PDOException $ex) {
      handle_error("Can not connect to the database. Please try again later.", $ex);
    }
  }

  function handle_error($msg, $ex=NULL) {
    header("HTTP/1.1 400 Invalid Request");
    header("Content-type: text/plain");
    print ("{$msg}\n");
    if ($ex) {
      print ("Error details: $ex \n");
    }
  }

  function head() {
    $output = "<head><title>My Movie Database (MyMDb)</title>
		<meta charset='utf-8' />
		<link href='favicon.png' type='image/png' rel='shortcut icon' />
		<link href='bacon.css' type='text/css' rel='stylesheet' />
    </head>";
    echo $output;
  }

  function banner() {
    $output = "<div id='banner'>
    <a href='mymdb.php'><img src='mymdb.png' alt='banner logo' /></a>
    My Movie Database</div>";
    echo $output;
  }

  function validator() {
    $output = "<div id='w3c'>
    <a href='https://webster.cs.washington.edu/validate-html.php'><img src='w3c-html5.png' alt='Valid HTML5' /></a>
    <a href='https://webster.cs.washington.edu/validate-css.php'><img src='w3c-css.png' alt='Valid CSS' /></a></div>";
    echo $output;
  }

  function form() {
    $output = "<form action='search-all.php' method='get'>
    <fieldset><legend>All movies</legend><div>
    <input name='firstname' type='text' size='12' placeholder='first name' autofocus='autofocus' />
    <input name='lastname' type='text' size='12' placeholder='last name' />
    <input type='submit' value='go' /></div></fieldset></form>
    <form action='search-kevin.php' method='get'>
    <fieldset><legend>Movies with Kevin Bacon</legend><div>
    <input name='firstname' type='text' size='12' placeholder='first name' />
    <input name='lastname' type='text' size='12' placeholder='last name' />
    <input type='submit' value='go' /></div></fieldset></form>";
    echo $output;
  }

  function frontPageSetUp() {
    $output = "<h1>The One Degree of Kevin Bacon</h1>
    <p>Type in an actor's name to see if he/she was ever in a movie with Kevin Bacon!</p>
    <p><img src='kevin_bacon.jpg' alt='Kevin Bacon' /></p>";
    echo $output;
  }

  function checkParam($msg) {
    if (isset($_GET["firstname"]) && isset($_GET["lastname"])) {
      $db = get_PDO();
      if ($msg === "kevin") {
        get_kevin_movies($_GET["firstname"], $_GET["lastname"], $db);
      } else {
        get_all_movies($_GET["firstname"], $_GET["lastname"], $db);
      }
    } else {
      handle_error("Both first name and last name values are required.");
    }
  }

  function get_kevin_movies($firstname, $lastname, $db) {
    try {
      $rows_all = queryAll($firstname, $lastname, $db);
      $html = "";
      if (count($rows_all) == 0) {
        $html = "<p> Actor {$_GET["firstname"]} {$_GET["lastname"]} not found.</p>";
      } else {
        $rows = queryKevin($firstname, $lastname, $db);
        $html = "";
        if (count($rows) == 0) {
          $html = "<p>{$firstname} {$lastname} wasn't in any film with Kevin Bacon.<p>";
        } else {
          $html = "<h1>Results for {$firstname} {$lastname}</h1><p>Films with {$firstname} {$lastname} and Kevin Bacon</p><table><table><tr id = 'title'><td>#</td><td>Title</td><td>Year</td></tr>";
          $index = 1;
          foreach ($rows as $row) {
            $html .= "<tr><td>" . $index . "</td><td>" . $row["name"] . "</td><td>" . $row["launch"] . "</td></tr>";
            $index += 1;
          }
          $html .= "</table>";
        }
      }
      echo $html;
    } catch (PDOException $ex) {
        handle_error("Error searching {firstname} {lastname} in database. Please try again later ", $ex);
    }
  }

  function get_all_movies($firstname, $lastname, $db) {
    try {
      $rows = queryAll($firstname, $lastname, $db);
      if (count($rows) == 0) {
        $html = "<p> Actor {$_GET["firstname"]} {$_GET["lastname"]} not found.</p>";
      } else {
        $index = 1;
        $html = "<h1>Results for {$firstname} {$lastname}</h1><p>All Films</p><table><table><tr id = 'title'><td>#</td><td>Title</td><td>Year</td></tr>";
        foreach ($rows as $row) {
          $html .= "<tr><td>" . $index . "</td><td>" . $row["name"] . "</td><td>" . $row["launch"] . "</td></tr>";
          $index += 1;
        }
        $html .= "</table>";
      }
      echo $html;
    } catch (PDOException $ex) {
        handle_error("Error searching {firstname} {lastname} in database. Please try again later ", $ex);
    }
  }

  function queryAll($firstname, $lastname, $db) {
    $qry = "SELECT name, launch
            FROM roles r
            JOIN movies m ON r.movie_id = m.id
            JOIN
            (SELECT id
            FROM actors
            WHERE first_name LIKE :firstname
            AND last_name = :lastname
            ORDER BY film_count DESC, id ASC
            LIMIT 1) AS found
            ON found.id = r.actor_id
            ORDER BY launch DESC, name ASC";
    $stmt = $db->prepare($qry);
    $params = array("firstname" => $firstname . "%", "lastname" => $lastname);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    return $rows;
  }

  function queryKevin($firstname, $lastname, $db) {
    $qry = "SELECT name, launch
            FROM roles r
            JOIN movies m ON r.movie_id = m.id
            JOIN
            (SELECT id
            FROM actors
            WHERE first_name LIKE :firstname
            AND last_name = :lastname
            ORDER BY film_count DESC, id ASC
            LIMIT 1) AS found
            ON found.id = r.actor_id
            WHERE r.movie_id IN
            (SELECT roles.movie_id
            FROM actors
            JOIN roles ON actors.id = roles.actor_id
            WHERE first_name='kevin' AND last_name='bacon')
            ORDER BY launch DESC, name ASC;";
      $stmt = $db->prepare($qry);
      $params = array("firstname" => $firstname . "%", "lastname" => $lastname);
      $stmt->execute($params);
      $rows = $stmt->fetchAll();
      return $rows;
  }
?>