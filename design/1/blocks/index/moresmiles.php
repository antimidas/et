<?php
/**
 |--------------------------------------------------------------------------|
 |   https://github.com/3evils/                                             |
 |--------------------------------------------------------------------------|
 |   Licence Info: WTFPL                                                    |
 |--------------------------------------------------------------------------|
 |   Copyright (C) 2020 Evil-Trinity                                        |
 |--------------------------------------------------------------------------|
 |   A bittorrent tracker source based on an unreleased U-232               |
 |--------------------------------------------------------------------------|
 |   Project Leaders: AntiMidas,  Seeder                                    |
 |--------------------------------------------------------------------------|
 |   All other snippets, mods and contributions for this version from:      |
 | CoLdFuSiOn, *putyn, pdq, djGrrr, Retro, elephant, ezero, Alex2005,       |
 | system, sir_Snugglebunny, laffin, Wilba, Traffic, dokty, djlee, neptune, |
 | scars, Raw, soft, jaits, Melvinmeow, RogueSurfer, stoner, Stillapunk,    |
 | swizzles, autotron, stonebreath, whocares, Tundracanine , son            |
 |                                                                                                                            |
 |--------------------------------------------------------------------------|
                 _   _   _   _     _   _   _   _   _   _   _
                / \ / \ / \ / \   / \ / \ / \ / \ / \ / \ / \
               | E | v | i | l )-| T | r | i | n | i | t | y )
                \_/ \_/ \_/ \_/   \_/ \_/ \_/ \_/ \_/ \_/ \_/
*/
require_once '/var/www/html/include/bittorrent.php';
require_once '/var/www/html/include/user_functions.php';
require_once '/var/www/html/include/bbcode_functions.php';
require_once '/var/www/html/include/html_functions.php';
require_once '/var/www/html/polls.php';
require_once '/var/www/html/include/class/class_user_options.php';
require_once '/var/www/html/include/class/class_user_options_2.php';
require_once '/var/www/html/include/emoticons.php';
dbconn(true);
loggedinorreturn();


$count = 0;
	while ((list($code, $url) = each($smilies))) {
		if ($count % 20 == 0) echo "<script type='text/javascript' src='shout.js'></script>
<p>";
		echo"   <a href=\"javascript: SmileIT('" . str_replace("'", "\'", $code) . "','shbox','shbox_text')\" aria-label='Dismiss alert' data-close><img src='./pic/smilies/" . $url . "' alt='' /></a>     ";
		$count++;
		if ($count % 20 == 0) echo "</p>";
	}
?>
