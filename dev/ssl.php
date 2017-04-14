<?php

$repo = 'ssl';//$_GET['repo'];
$repo = preg_replace("/[^\w_]/", "", $repo);

$hg = $_GET['hg'];
$hg = preg_replace("/[^\w \/\\\.\-_\"\[\],]/s", "", $hg);
$hg = str_replace('\\"', '"', $hg);

chdir(dirname(__FILE__));
#var_dump($_GET);
if(strlen($repo))
{
        if(true)
        {
                passthru("hg clone <MERCURIAL-REPO> repo/{$repo}");
                chdir(__DIR__ . "/repo/{$repo}");
                passthru("hg update");
				file_put_contents(".hg/hgrc", "[paths]\ndefault = <MERCURIAL-REPO>");

				exec('cp shop/config.sample.php shop/config.php');
				exec('cp shop/application/config/config.sample.php shop/application/config/config.php');
				exec('cp shop/application/config/ridishop.sample.php shop/application/config/ridishop.php');
				exec('cp ridibooks/v2/config.sample.php ridibooks/v2/config.php');
        }
        if(strlen($hg))
        {
                chdir(__DIR__ . "/repo/{$repo}");
                passthru("hg {$hg} 2>&1");
        }
}
?>
</xmp></pre>
</body>
</html>

