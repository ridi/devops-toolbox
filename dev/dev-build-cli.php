<?php

$repo = 'kk';
chdir(dirname(__FILE__));

passthru("git clone <GIT-REPO> repo/{$repo} 2>&1");
exec("sed 's/TODO/{$repo}/' config.local.php > repo/{$repo}/include/config.local.php"); 

exec("cp post-checkout repo/{$repo}/.git/hooks");
