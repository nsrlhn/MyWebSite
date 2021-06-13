<!DOCTYPE html>
<html>
<head>
	<title>nsrlhn</title>
	<link rel="stylesheet" type="text/css" href="pages.css">
</head>
<body>

	<div class="maindiv" id="div2">
		<u><h2 class="h2">Bicycle:</h2></u>
		<?php
			$dir = "../images/bicycle/";
			$imgs = scandir($dir);
			foreach ($imgs as $i) {
				$ext =  pathinfo($i, PATHINFO_EXTENSION);
				if ($ext == "jpg" || $ext == "JPG" || $ext == "jpeg" || $ext == "png") {
  					echo '<img src="'.$dir.$i.'"/>';
				}
			}
		?>
	</div>
</body>
</html>