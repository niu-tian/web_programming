<!DOCTYPE html>
<html>
<?php
	session_start();
	if ($_SESSION["user"] == null) {
		header("Location: start.php");
		exit();
	} else {
		include("common.php");
		head();
		echo "<body>";
		banner();
		echo "<div id='main'>"; // start of main div
		title();
		generateToDoList();
		logout();
		echo "</div>"; // end of main div
		footer();
		echo "</body>";
	}
?>
</html>