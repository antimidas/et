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
//==Qlogin by stonebreath and laffin
if ($CURUSER['class'] >= UC_STAFF && $id == $CURUSER['id']) {
    $hash1 = $mc1->get_value('hash1_' . $id);
    if ($hash1 === false) {
        $res = sql_query("SELECT hash1 FROM users WHERE id = " . sqlesc($CURUSER['id']) . " AND class >= " . UC_STAFF) or sqlerr(__FILE__, __LINE__);
        $hash1 = mysqli_fetch_assoc($res);
        $mc1->cache_value('hash1_' . $id, $hash1, $INSTALLER09['expires']['user_hash']);
    }
    $arr = $hash1;
    if ($arr['hash1'] != '') {
        $HTMLOUT.= "<div class='row'><div class='card secondary large-12 columns'>
		<h4 class='subheader'>{$lang['userdetails_login_link']}
		<a class='button float-right' href='createlink.php?action=reset&amp;id=" . (int)$CURUSER['id'] . "' target='_blank'>{$lang['userdetails_login_reset']}</a>
		</h4>
		<p>{$INSTALLER09['baseurl']}/pagelogin.php?qlogin=" . htmlsafechars($arr['hash1']) . "<p>
		</div></div>";
    } else {
        $HTMLOUT.= "<div class='row'><div class='card secondary large-12 columns'>
			<h4 class='subheader'>{$lang['userdetails_login_link']}</h4>
			<p><a href='createlink.php?id=" . (int)$CURUSER['id'] . "' target='_blank'>{$lang['userdetails_login_create']}</a></p>
		</div></div>";
    }
}
//==End
// End Class
// End File
