<!DOCTYPE html>
<html>
<?php
	session_start();
	include("common.php");
	head();
	echo "<body>";
	banner();
	echo "<div id='main'>"; // start of main div
	handle_error();
	frontPageSetUp();
	loginForm();
	lastLogin(); // add last log in time here
	echo "</div>"; // end of main div
	footer();
	echo "</body>";
?>
</html>