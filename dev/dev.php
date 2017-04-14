<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
<pre><xmp><?php

ob_implicit_flush(true);
ob_end_flush();

$repo = $_GET['repo'];
$repo = preg_replace("/[^\w_\/]/", "", $repo);

$git = $_GET['git'];
$git = preg_replace("/[^\w \/\\\.\-_\"\[\],]/s", "", $git);
$git = str_replace('\\"', '"', $git);

$make_command = $_GET['make_command'];
$make_command = preg_replace("/[^\w_\-\/]/", "", $make_command);

chdir(__DIR__);

if (strlen($repo)) {
	if( $_GET['make']) {
		chdir(__DIR__."/repo/{$repo}");
		passthru("COMPOSER_HOME='".__DIR__."/repo' make " . $make_command . " 2>&1");
#		chdir(__DIR__."/repo/{$repo}");
#		chdir("platform/");
#		passthru("COMPOSER_HOME='".__DIR__."/repo' make 2>&1");
	}
	if( $_GET['build']) {
		passthru("git clone <GIT-REPO1> repo/{$repo} 2>&1");
		passthru("git clone <GIT-REPO2> repo/{$repo} 2>&1");
		exec("sed 's/TODO/{$repo}/' config.local.php > repo/{$repo}/include/config.local.php");

		exec("cp post-checkout repo/{$repo}/.git/hooks");

		chdir("repo/{$repo}");
		exec("git submodule init");
		exec("git submodule update");
		exec("git pull");
	}

	if (strlen($git)) {
		if ($repo == 'shop') {
			chdir(__DIR__ . "/../shop");
			passthru("hg {$git} 2>&1");
			exec("chmod g+w -R .");
		} else {
			chdir(__DIR__ . "/repo/{$repo}");
			passthru("git {$git} 2>&1");
			exec("chmod g+w -R .");
		}
	}
}
?>
</xmp></pre>
</body>
</html>

