<!DOCTYPE html>
<html>
	<?php
		include("common.php");
		head();
		echo "<body><div id='frame'>";
		banner();
		echo "<div id='main'>";
		checkParam("all");
		form();
		echo "</div>";
		validator();
		echo "</div></body>";
	?>
</html>