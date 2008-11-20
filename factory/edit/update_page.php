<?php

function resolve_url ($file) {
	$scheme = isset($_SERVER['HTTPS']) ? 'https' : 'http';
	$host   = $_SERVER['HTTP_HOST'];
	$path   = rtrim(dirname($_SERVER['PHP_SELF']), '/');
	return "$scheme://$host$path/$file";
}

$fp = fopen("/tmp/goose_lock.txt", "w+");

if(flock($fp, LOCK_EX)) {
	$author  = $_SERVER['REMOTE_USER'];
	$path    = $_SERVER['DOCUMENT_ROOT'] . "/../pages";
	$file    = $_REQUEST['file'];
	$content = preg_replace('/\r(\n)?/', "\n", $_REQUEST['content']);
	$log     = $_REQUEST['log'];

	// echo "Write to: $path/$file.page<br>\n";
	if($page = fopen($path . '/' . $file . '.page', 'w')) {
		fwrite($page, $content);
		fclose($page);

		// echo "Run: cd $path && git add $file.page && git commit -m '$log' --author='$author <>' && cd .. && make<br>\n";
		if($cmd = popen("cd $path && git add $file.page && git commit -m '$log' --author='$author <>' && cd .. && make", 'r')) {
			while($line = fgets($cmd))
				; // echo $line . "<br>\n";
			pclose($cmd);
			header('Location: ' . resolve_url("../$file"));
		}
	}

	flock($fp, LOCK_UN);
} else {
	echo "Couldn't lock the file !";
}

fclose($fp);

?>