<?php
$dir    = '.';
$files = scandir($dir);
$output = null;

foreach ($files as $file) {
	if (!is_dir($file) && $file != 'index.php' &&  $file != '.DS_Store') {
		$output .= <<<HTML
		<a href="$file">$file &#187;</a><br>
HTML;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Authorize.net Prototypes</title>
	<style>
	body {
		font-family: arial, helvetica, sans-serif;
	}
	#wrapper {
	margin: 20px auto;
	padding: 20px;
	width: 400px;
	border: 1px solid #666;
	line-height: 1.5em;
	}

	#wrapper a:link, #wrapper a:visited {
		color: blue;
		text-decoration: none;
	}
	#wrapper a:hover {
		color: darkred;
		text-decoration: underline;
	}
	</style>
</head>
<body>
<div id="wrapper">
<?= $output ?>
</div>
</body>
</html>