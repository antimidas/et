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
     _   _   _   _     _   _   _   _   _   _   _ 
 / \ / \ / \ / \   / \ / \ / \ / \ / \ / \ / \
| E | v | i | l )-| T | r | i | n | i | t | y )
 \_/ \_/ \_/ \_/   \_/ \_/ \_/ \_/ \_/ \_/ \_/

*/
/** latestuser index **/
if (($latestuser_cache = $mc1->get_value('latestuser')) === false) {
    $latestuser_cache = mysqli_fetch_assoc(sql_query('SELECT id, username, class, donor, warned, enabled, chatpost, leechwarn, pirate, king FROM users WHERE status="confirmed" ORDER BY id DESC LIMIT 1'));
    $latestuser_cache['id'] = (int)$latestuser_cache['id'];
    $latestuser_cache['class'] = (int)$latestuser_cache['class'];
    $latestuser_cache['warned'] = (int)$latestuser_cache['warned'];
    $latestuser_cache['chatpost'] = (int)$latestuser_cache['chatpost'];
    $latestuser_cache['leechwarn'] = (int)$latestuser_cache['leechwarn'];
    $latestuser_cache['pirate'] = (int)$latestuser_cache['pirate'];
    $latestuser_cache['king'] = (int)$latestuser_cache['king'];
    $mc1->cache_value('latestuser', $latestuser_cache, $INSTALLER09['expires']['latestuser']);
}
$latestuser = '
<div class="header panel panel-default">
	<div class="panel-heading">
		<label for="checkbox_4" class="text-left">' . $lang['index_lmember'] . '</label>
	</div>

		<div class="container-fluid panel-body">
		' . $lang['index_wmember'] . '
		<b>' . format_username($latestuser_cache) . '!</b>
		</div>
</div>';
//==MemCached latest user
$HTMLOUT.= $latestuser;
//==
// End Class
// End File
